#!/usr/bin/perl
use URI::Escape;
use HTML::Entities;
use Encode;
use utf8;

$file = shift;
$yml = shift;
$display_text = shift;

my %question;
$question{'source'} = uri_unescape($file);
$question{'source'} =~ s/^[^\/]+\///;
if ($question{'source'} =~ /questions\/base\/(\d{4})\/qSEQ\d{4}(\d{4,5})([a-z])?.html/i) {
  $question{'numero'} = uc($2.$3);
#  $question{'annee'} = $1;
  $type = $3;
}

open(FILE, $file) ;
@string = <FILE>;
$string = "@string";
close FILE;
utf8::decode($string);
$string = decode_entities($string);
$string =~ s/(<[^a!]\w*)[^>]*>/$1>/g;
$string =~ s/<a[^>]*href=["']([^"']+)["'][^>]*>/<a href='$1'>/g;
$string =~ s/<\/?div>//g;
$string =~ s/\r//g;
$string =~ s/ +/ /g;
$string =~ s/\n\s+/\n/g;
$string =~ s/\s+\n/\n/g;
$string =~ s/^(.*\n)*<!-- START : primary -->//;
$string =~ s/\n<!-- END : primary -->(\n.*)*$//;
$string =~ s/<!--[^>]*-->//g;
$string =~ s/\n([^<])/ $1/g;
$string =~ s/<br>/<br\/>/g;
$string =~ s/\n<\//<\//g;
$string =~ s/> +/>/g;
$string =~ s/(>)(<[phult1-9]+)/$1\n$2/g;
$string =~ s/\n+/\n/;


if ($display_text) {
  utf8::encode($string);
  print $string;
  exit;
}

$read_txt = 0;
foreach $line (split /\n/, $string) {
  if ($line =~ /<h1>(.*)<\/h1>/) {
    $question{'titre'} = $1;
  } elsif ($line =~ /<h2>(\d+).*législature<\/h2>/) {
    $question{'legislature'} = $1;
  } elsif ($line =~ /<h2>(Question.*)\s+n°\s*(\d+[a-z]?)\s/i) {
    $question{'type'} = $1;
    $num = uc($2);
    if ($num != $question{'numero'}) {
      print "Erreur numero question : ".$num." dans texte mais ".$question{'numero'}." dans url ".$question{'source'};
    }
  } elsif ($line =~ /<b>(M[\.mle]+) (.*)\s*<\/b>\s*\((.* - .*)\)\s*<\/h2>$/) {
    $nom_auteur = $2;
    $details = $3;
    $sexe = $1;
    if ($sexe =~ /[mle]/) {
      $sexe = "F";
    } else {
      $sexe = "H";
    }
    $question{'auteur'} = "$nom_auteur - $sexe - $details";
  } elsif ($line =~ /<h3>.*publi.*JO.*\s(\d+)\/(\d+)\/(\d{4})([ \-]+page\s+(\d+))?\s*<\/h3>/) {
    if (! $question{'date_publi'} ) {
      $question{'date_publi'} = sprintf("%04d-%02d-%02d",$3,$2,$1);
      $question{'page_question'} = $5;
      $read_txt++;
    } else {
      $question{'date_reponse'} = sprintf("%04d-%02d-%02d",$3,$2,$1);
      $question{'page_reponse'} = $5;
      if (! $question{'page_question'} && $question{'date_publi'} == $question{'date_reponse'}) {
        $question{'page_question'} = $question{'page_reponse'};
      }
    }
  } elsif ($line =~ /<h3>Concerne le thème\s*:\s*(.*)<\/h3>/) {
    $question{'themes'} = $1;
  } elsif ($line =~ /<[^>]+>.*réponse du (.*)<\/[^>]+>/i) {
    $question{'ministere'} = $1;
    $question{'ministere'} =~ s/\s*En attente.*$//;
    $read_txt++;
  } elsif ($line =~ /<h3>Rappelle la question (n°)? ?<a[^>]+>(n°)? ?(\d+[a-z]?)[^\d]/i) {
    $rappel_question = $3;
  } elsif ($read_txt && $line =~ />(\S\s*)+</) {
    if ($line =~/<p>La question (a été retirée( pour cause de )?|est )(.*)?</) {
      $question{'motif_retrait'} = $3;
      if (! $question{'motif_retrait'}) {
        $question{'motif_retrait'} = "retrait";
      }
      $read_txt++;
      next;
    }
    $texte = $line;
    $texte =~ s/(\s*<br\/?>)+/<\/p><p>/g;
    $texte =~ s/<\/p>(<\/?p><\/?p>)+/<\/p>/g;
    $texte =~ s/"([^"]*)"/« $1 »/g;
    if ($read_txt == 1) {
      $question{'question'} .= $texte;
    } else {
      $question{'reponse'} .= $texte;
    }
  }
}

if ($rappel_question) {
  $leg = $question{legislature};
  $question{'question'} =~ s/(question n°\s*)($rappel_question)/<a href='\/question\/QE\/$leg\/$2'>$1$2<\/a>/;
}

if (! $question{ministere} ) {
  $question{ministere} = $question{'question'};
  $question{ministere} =~ s/^<p>M[.mle]+ $nom_auteur (\S+\s+)+M[.mle]+ l[ea'] ?([^<\.]+)[\.<].*$/$2/;
  $question{ministere} =~ s/(que|sur|les termes) .*$//;
  $question{ministere} =~ s/^(ministre d'[ÉEé]tat, )?ministre/Ministère/;
  $question{ministere} =~ s/^secrétaire/Secrétariat/;
}

foreach $k (keys %question) {
  utf8::encode($question{$k});
}

if ($yml) {
  foreach $k (keys %question) {
    print "  ".lc($k).": ".$question{$k}."\n";
  }
  exit;
}

print '{"source": "'.$question{'source'}.'", "legislature": "'.$question{'legislature'}.'", "type": "'.$question{'type'}.'", "numero": "'.$question{'numero'}.'", "date_question": "'.$question{'date_publi'}.'", "date_reponse": "'.$question{'date_reponse'}.'", "page_question": "'.$question{'page_question'}.'", "page_reponse": "'.$question{'page_reponse'}.'", "ministere": "'.$question{'ministere'}.'", "theme":"'.$question{'theme'}.'", "titre": "'.$question{'titre'}.'", "question": "'. $question{'question'}.'", "reponse": "'.$question{'reponse'}.'", "motif_retrait": "'.$question{'motif_retrait'}.'", "auteur": "'.$question{'auteur'}.'" } '."\n";

