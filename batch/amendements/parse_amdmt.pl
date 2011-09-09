#!/usr/bin/perl

use HTML::Entities;
use URI::Escape;
use Encode;
use utf8;
require "../common/common.pm";

$file = shift;
$yml = shift;
$display_text = shift;

my %amdmt;

open(FILE, $file);
@string = <FILE>;
$string = "@string";
close FILE;
utf8::decode($string);
$string = decode_entities($string);
$string =~ s/<br\/?>//ig;
$string =~ s/\r//g;
$string =~ s/[\n\s]+/ /g;
$string =~ s/^.*<body> *//i;
$string =~ s/ *<\/body>.*$//i;
$string =~ s/<!--/\n<!--/g;
$string =~ s/ *\n *(<!--\s*fin[^>]*-->) */$1\n/ig;
$string =~ s/(<t\w+) *[^>]* colspan="(\d+)"[^>]*>/$1colspan$2>/ig;
$string =~ s/(<[^aA!][\w]*) *[^>]*>/$1>/g;
$string =~ s/<a[^>]*href=["']([^"']+)["'][^>]*>/<a href='$1'>/ig;
$string =~ s/<br\/?>//ig;
$string =~ s/colspan(\d+)/ colspan='$1'/g;
$string =~ s/> +/>/g;
$string =~ s/ *(<t[dh]>)<\/?p>/$1/ig;
$string =~ s/ *<\/?p>(<\/t[dh]>)/$1/ig;
$string =~ s/"/&quot;/g;
$string =~ s/\n((<[^=>]+>(.|Objet)?)+\n)+/\n/g;
$string =~ s/ *\n */\n/g;
$string =~ s/ *, */, /g;
$string =~ s/\\/\//g;
$string =~ s/(position_amdt=[^>]* -->)/$1\nNEXT\n/ig;
#$string =~ s/<div><table><tr><td><img><\/p><p><strong>/\n<!-- contexte=/ig;
#$string =~ s/<\/strong><\/td><td><strong>[^\n]+<\/strong><\/p><p>\(/ -->\n<!-- etape_leg=/ig;
#$string =~ s/\)<\/p><p>\(n\W+/ -->\n<!-- lois=/ig;
#$string =~ s/\)<\/td><td><strong>n\W+/ -->\n<!-- numero=/ig;
#$string =~ s/<\/strong><\/p><p>/ -->\n<!-- debut_date=/ig;
#$string =~ s/<\/td><\/tr><\/table><hr><table><tr><td><\/td><td><h1>/ -->\n<!-- debut_type=/ig;
#$string =~ s/<\/h1>[^\n]*\n/ -->\n/ig;

if ($display_text) {
  utf8::encode($string);
  print $string;
  exit;
}

$file =~ s/^.*(http.*)$/$1/i;
$file = uri_unescape($file);
# Récupération des informations identifiantes à partir de l'url plus sure :
if ($file =~ /\/amendements(\/commissions)?\/(\d{4}-\d{4})\/(\d+)\//i) {
  $session = $2;
  $loi = law_numberize($3, $session);
  $source = $file;
  $source =~ s/jeu_complet/Amdt_##ID##/i;
}

reset_amdmt();
foreach $line (split /\n/, $string) {
  if ($line eq "NEXT") {
    checkout();
  } elsif ($line =~ /<\!-- numero_amdt_sans_rect=\s*([^\>]+)\s+-->/i) {
    $amdmt{'numero'} = $1;
  } elsif ($line =~ /<!-- niveau_rect=\s*([^\>]+)\s+-->/i) {
    $amdmt{'rectif'} = $1;
  } elsif ($line =~ /<!-- numero_amdt_pere_sans_rect=\s*([^\>]+)\s+-->/i) {
    $amdmt{'parent'} = $1;
  } elsif ($line =~ /<!-- debut_signataires -->(.*)<!-- fin_signataires -->/i) {
    $amdmt{'auteurs'} = $1;
  } elsif ($line =~ /<!-- debut_aunomde -->(.*)<!-- fin_aunomde -->/i) {
    $amdmt{'auteurs'} .= " $1";
  } elsif ($line =~ /<!-- debut_avis_commission -->(.*)<!-- fin_avis_commission -->/i) {
    $amdmt{'aviscomm'} = $1;
  } elsif ($line =~ /<!-- debut_avis_gouvernement -->(.*)<!-- fin_avis_gouvernement -->/i) {
    $amdmt{'avisgouv'} = $1;
  } elsif ($line =~ /<!-- debut_sort -->(.*)<!-- fin_sort -->/i) {
    $amdmt{'sort'} = sortseance($1);
  } elsif ($line =~ /<!-- debut_subdivision -->(.*)<!-- fin_subdivision -->/i) {
    $amdmt{'sujet'} = lc($1);
  } elsif ($line =~ /<!-- debut_dispositif -->(.*)<!-- fin_dispositif -->/i) {
    $amdmt{'texte'} = $1;
  } elsif ($line =~ /<!-- debut_objet -->(.*)<!-- fin_objet -->/i) {
    $amdmt{'expose'} = $1;
  } elsif ($line =~ /<!-- debut_([^\>]+) -->(.*)<!-- fin_/i) {
    $amdmt{$1} = $2;
  } elsif ($line =~ /<!-- ([^\>]+)=\s*([^\>]*)\s+-->/i) {
    $amdmt{$1} = $2;
  } elsif ($line =~ /<strong>(Direction de la séance|commission de[^\<]+)<\/strong>/) {
    $amdmt{'contexte'} = $1;
    if ($line =~ />\s*(\d+e?r? \S* \d+)\s*</) {
      $amdmt{'date'} = join '-', datize($1);
    } elsif ($line =~ /(\d{1,2})\/(\d{2})\/(\d{4})/) {
      $amdmt{'date'} = $3.'-'.$2.'-'.sprintf('%02d', $1);
    }
    if ($line =~ />\s*\(\s*n°\s*(.*)\s*\)\s*</i) {
      $amdmt{'ref_lois'} = reflois($1);
    }
    if ($line =~ /<h1>(.*)<\/h1>/) {
      $amdt{'type'} = lc($1);
    }
  } else {
    if ($line =~ /amendement.*(irrecevable|retir)/i && !$amdmt{'sort'}) {
      $amdmt{'sort'} = sortseance($line);
    }
    $amdmt{'expose'} .= "<p>$line</p>";
  }
}

sub reset_amdmt {
  foreach $k (keys %amdmt) {
    $amdmt{$k} = "";
  }
  $amdmt{'loi'} = $loi;
}

sub checkout {
  $amdmt{'source'} = $source;
  $amdmt{'source'} =~ s/##ID##/$amdmt{'numero'}/;
  $amdmt{'texte'} = clean_texte($amdmt{'texte'});
  $amdmt{'expose'} = clean_texte($amdmt{'expose'});
  $amdmt{'auteurs'} = clean_auteurs($amdmt{'auteurs'});
  foreach $k (keys %amdmt) {
    utf8::encode($amdmt{$k});
  }
  if ($yml) {
    print "\namendement: ".$amdmt{'loi'}."-".$amdmt{'numero'}."\n";
    foreach $k (keys %amdmt) {
      print "  ".lc($k).": ".$amdmt{$k}."\n";
    }
  } else {
    print '{"source": "'.$amdmt{'source'}.'", "loi": "'.$amdmt{'loi'}.'", "numero": "'.$amdmt{'numero'}.'", "serie": "'.$amdmt{'serie'}.'", "rectif": "'.$amdmt{'rectif'}.'", "parent": "'.$amdmt{'parent'}.'", "date": "'.$amdmt{'date'}.'", "auteurs": "'.$amdmt{'auteurs'}.'", "commission": "'.$amdmt{'commission'}.'", "avis_comm": "'.$amdmt{'aviscomm'}.'", "avis_gouv": "'.$amdmt{'avisgouv'}.'", "sort": "'.$amdmt{'sort'}.'", "sujet": "'.$amdmt{'sujet'}.'", "texte": "'.$amdmt{'texte'}.'", "expose": "'.$amdmt{'expose'}.'" } '."\n";
  }
  reset_amdmt();
}

sub reflois {
  my $lois = shift;
  $lois =~ s/\s*,\s*/,/g;
  my $reflois = "";
  while ($lois =~ /(\d+)(| rect[\s\.ifiébs]*)( \((\d{4}-\d{4})\)|)/g) {
    if ($4) {
      $reflois .= law_numberize($1,$4).",";
    } else {
      $reflois .= law_numberize($1,$session).",";
    }
  }
  chop($numeros_loi);
  return $reflois
}

sub sortseance {
  my $sort = shift;
  if ($sort =~ /irrecevab/i) {
    $sort = 'Irrecevable';
  } elsif ($sort =~ /retiré.*séance/i) {
    $sort = 'Retiré avant séance';
  } elsif ($sort =~ /retiré/i) {
    $sort = 'Retiré';
  } elsif ($sort =~ /non.*(soutenu|défendu)/i) {
    $sort = 'Non soutenu';
  } elsif ($sort =~ /tombe/i) {
    $sort = 'Tombe';
  } elsif ($sort =~ /rejet/i) {
    $sort = 'Rejeté';
  } elsif ($sort =~ /adopt/i) {
    $sort = 'Adopté';
  }
  return $sort;
}

sub clean_auteurs {
  my $txt = shift;
  $txt =~ s/([A-ZÀÉÈÊËÎÏÔÙÛÜÇ])(\w+ ?)/$1\L$2/g;
#  $txt =~ s/\s*\<\/?[^\>]+\>//g;
#  $txt =~ s/\s+M([\.mles]+)\s*,\s*/ M$1 /g;
#  $txt =~ s/([a-z])\s+(M[\.Mml])/$1, $2/g;
#  $txt =~ s/,\s*M[\s\.mle]+\s*,/,/g;
  $txt =~ s/\s+e{1,2}t\s+/, /g;
  $txt =~ s/^et\s+/, /g;
  $txt =~ s/\s+,/,/g;
#  $txt =~ s/\s*[,]?\s*les\s+[cC]ommissaires.*$//g;
#  $txt =~ s/\s*[,]?\s*[rR]apporteur[\s,a-zéèêà\-']*M(.*)/, M\1/g;
#  $txt =~ s/\s*[,]?\s*[rR]apporteur[\s,a-zéèêà\-']*//g;
  $txt =~ s/(,\s*,|,+)/,/g;
  $txt =~ s/,+/,/g;
  $txt =~ s/^\s*,\s*//g;
  $txt =~ s/\s*,\s*$//g;
  $txt =~ s/ et(\W)/\1/g;
#  $txt =~ s/([^,\s])\s*(les\s*membres.*groupe.*)$/\1, \2/i;
  return $txt
}

sub clean_texte {
  my $txt = shift;
  $txt =~ s/<(\/)?div>/<$1p>/ig;
  $txt =~ s/<!--[^>]*>//g;
  $txt =~ s/^([^<])/<p>$1/i;
  $txt =~ s/<\/p>([^<])/<\/p><p>$1/ig;
  $txt =~ s/([^>])$/$1<\/p>/i;
  $txt =~ s/([^>])<p>/$1<\/p><p>/ig;
  $txt =~ s/\s*<p>(\s*<\/?p>\s*)*\s*/<p>/ig;
  $txt =~ s/\s*(\s*<\/?p>\s*)*<\/p>\s*/<\/p>/ig;
  return $txt
}


# handle identiques/series?
#  if ($lettre =~ /[a-z]/i) {
#    $amdmt{'numero'} = $num.uc($lettre);
#  } else {
#    $amdmt{'numero'} = (10000*$lettre+$num);
#  }
