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
$string =~ s/\r//g;
$string =~ s/[\n\s]+/ /g;
$string =~ s/^.*<body> *//i;
$string =~ s/ *<\/body>.*$//i;
$string =~ s/<br style='page-break-before:always'><br>/##NEXT##/ig;
$string =~ s/\s*<[\s\/]*br[\s\/]*>\s*/<\/p><p>/ig;
$string =~ s/<![\s\-]*\[if[^\]]+\][\s\-]*>//ig;
$string =~ s/<![\s\-]*\[endif\][\s\-]*>//ig;
$string =~ s/<![\s\-]*\/\*[^\>]*>//g;
$string =~ s/<!--/\n<!--/g;
$string =~ s/ *\n *(<!--\s*fin[^>]*-->) */$1\n/ig;
$string =~ s/(<t\w+) *[^>]* colspan="(\d+)"[^>]*>/$1colspan$2>/ig;
$string =~ s/(<[^aA!][\w]*) *[^>]*>/$1>/g;
$string =~ s/<a[^>]*href=["']([^"']+)["'][^>]*>/<a href='$1'>/ig;
$string =~ s/<a href='\/([^']+)'>/<a href='http:\/\/www.senat.fr\/$1'>/ig;
$string =~ s/colspan(\d+)/ colspan='$1'/g;
$string =~ s/> +/>/g;
$string =~ s/ *(<t[dh]>)<\/?p>/$1/ig;
$string =~ s/ *<\/?p>(<\/t[dh]>)/$1/ig;
$string =~ s/"/\&quot;/g;
$string =~ s/\n((<[^=>]+>(.|Objet|\))?)+\n)+/\n/g;
$string =~ s/ *\n */\n/g;
$string =~ s/ *, */, /g;
$string =~ s/\\/\//g;
$string =~ s/(<div><strong>)/\n\1/g;
$string =~ s/##NEXT##/\nNEXT\n/g;
#$string =~ s/(position_amdt=[^>]* -->)/$1\nNEXT\n/ig;

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
    $amdmt{'parent'} =~ s/COM-//;
  } elsif ($line =~ /<!-- debut_signataires -->(.+)<!-- fin_signataires -->/i) {
    $amdmt{'auteurs'} = $1;
  } elsif ($line =~ /<!-- debut_aunomde -->(.+)<!-- fin_aunomde -->/i) {
    $amdmt{'auteurs'} .= ", $1";
  } elsif ($line =~ /<!-- debut_accordgouv -->(.+)<!-- fin_accordgouv -->/i) {
    $amdmt{'auteurs'} .= ", $1";
  } elsif ($line =~ /<!-- debut_avis_commission -->(.+)<!-- fin_avis_commission -->/i) {
    $amdmt{'aviscomm'} = $1;
    $amdmt{'aviscomm'} =~ s/ du Sénat//;
  } elsif ($line =~ /<!-- debut_avis_gouvernement -->(.+)<!-- fin_avis_gouvernement -->/i) {
    $amdmt{'avisgouv'} = $1;
    $amdmt{'avisgouv'} =~ s/ du Sénat//;
  } elsif ($line =~ /<!-- debut_sort -->(.+)<!-- fin_sort -->/i) {
    $amdmt{'sort'} = sortseance($1);
  } elsif ($line =~ /<!-- debut_subdivision -->(.+)<!-- fin_subdivision -->/i) {
    $tmpsujet = lc($1);
    $amdmt{'sujet'} =~ s/(.)$/$1 /;
    $amdmt{'sujet'} .= $tmpsujet;
  } elsif ($line =~ /<!-- debut_dispositif -->(.+)<!-- fin_dispositif -->/i) {
    $amdmt{'texte'} = $1;
  } elsif ($line =~ /<!-- debut_objet -->(.+)<!-- fin_objet -->/i) {
    $amdmt{'expose'} = $1;
  } elsif ($line =~ /<!-- debut_libelle_motion -->(.+)<!-- fin_libelle_motion -->/i) {
    $tmplibellemotion = lc($1);
    $amdmt{'sujet'} = "motion $tmplibellemotion".$amdmt{'sujet'};
  } elsif ($line =~ /<!-- debut_sous_subdivision -->(.+)<!-- fin_sous_subdivision -->/i) {
    $tmprefloi = lc($1);
    if ($tmprefloi =~ /^([eé]tat|\(?(rapport )?annex)/) {
      $amdmt{'sujet'} =~ s/(.)$/$1 /;
      $amdmt{'sujet'} .= $tmprefloi;
    } else {
      if ($tmprefloi =~ / [lo\.\s]+\d/) {
        $tmprefloi =~ s/^(division|art).* [lo\.\s]+(\d)/Art. L.O. $2/;
      } else {
        $tmprefloi =~ s/^(division|art)[^\d]+(\d)/Art. $2/;
      }
      $amdmt{'refloi'} = ucfirst($tmprefloi);
    }
  } elsif ($line =~ /<!-- debut_([^\>]+) -->(.+)<!-- fin_/i) {
    $amdmt{$1} = $2;
  } elsif ($line =~ /<!-- ([^\>]+)=\s*([^\>]+)\s+-->/i) {
    $amdmt{$1} = $2;
  } elsif ($line =~ /<strong>(Direction de la séance|commission (spéciale|d[eu])[^\<]+)<\/strong>/i) {
    $amdmt{'contexte'} = $1;
    if ($line =~ />\s*(\d+e?r? \S* \d+)\s*</) {
      $amdmt{'date'} = join '-', datize($1);
    } elsif ($line =~ /(\d{1,2})\/(\d{2})\/(\d{4})/) {
      $amdmt{'date'} = $3.'-'.$2.'-'.sprintf('%02d', $1);
    }
    if ($line =~ />\s*\(\s*n°\s*(.*)\s*\)\s*</i) {
      $amdmt{'refnumlois'} = refnumlois($1);
    }
    if ($line =~ /<h1>(Motion .*)<\/h1>/) {
      $amdmt{'sujet'} = lc($1);
    }
  } else {
    if ($line =~ /(amendement.*(irrecevable|retir)|retiré avant (réunion|séance))/i && (!$amdmt{'sort'} || $amdmt{'sort'} eq "Retiré")) {
      $amdmt{'sort'} = sortseance($line);
    }
    if ($amdmt{'texte'} eq "") {
      $amdmt{'texte'} .= "<p>$line</p>";
    } else {
      $amdmt{'expose'} .= "<p>$line</p>";
    }
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
  if ($amdmt{'contexte'} && $amdmt{'contexte'} ne "Direction de la séance") {
    $amdmt{'commission'} = $amdmt{'contexte'};
  }
  foreach $k (keys %amdmt) {
    utf8::encode($amdmt{$k});
  }
  if ($yml) {
    print "\namendement: ".$amdmt{'loi'}."-".$amdmt{'numero'}."\n";
    foreach $k (keys %amdmt) {
      print "  ".lc($k).": ".$amdmt{$k}."\n";
    }
  } else {
    print '{"source": "'.$amdmt{'source'}.'", "loi": "'.$amdmt{'loi'}.'", "numero": "'.$amdmt{'numero'}.'", "rectif": "'.$amdmt{'rectif'}.'", "parent": "'.$amdmt{'parent'}.'", "date": "'.$amdmt{'date'}.'", "auteurs": "'.$amdmt{'auteurs'}.'", "commission": "'.$amdmt{'commission'}.'", "aviscomm": "'.$amdmt{'aviscomm'}.'", "avisgouv": "'.$amdmt{'avisgouv'}.'", "sort": "'.$amdmt{'sort'}.'", "sujet": "'.ucfirst($amdmt{'sujet'}).'", "refloi": "'.$amdmt{'refloi'}.'", "texte": "'.$amdmt{'texte'}.'", "expose": "'.$amdmt{'expose'}.'" } '."\n";
  }
  reset_amdmt();
}

sub refnumlois {
  my $lois = shift;
  $lois =~ s/\s*,\s*/,/g;
  my $refnumlois = "";
  while ($lois =~ /(\d{1,2,3})(| rect[\s\.ifiébs]*)( \((\d{4}-\d{4})\)|)/g) {
    if ($4) {
      $refnumlois .= law_numberize($1,$4).",";
    } else {
      $refnumlois .= law_numberize($1,$session).",";
    }
  }
  chop($refnumlois);
  return $refnumlois;
}

sub sortseance {
  my $sort = shift;
  if ($sort =~ /irrecevab/i) {
    $sort = 'Irrecevable';
  } elsif ($sort =~ /retir.*avant.*(union|ance)/i) {
    $sort = 'Retiré avant séance';
  } elsif ($sort =~ /satisfait/i) {
    $sort = 'Satisfait';
  } elsif ($sort =~ /retir/i) {
    $sort = 'Retiré';
  } elsif ($sort =~ /non.*(soutenu|d.+fendu)/i) {
    $sort = 'Non soutenu';
  } elsif ($sort =~ /tomb/i) {
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
  $txt =~ s/Mm[. ]/MM./ig;
  $txt =~ s/\s+e+t\s+([A-ZÉl])/, $1/g;
  $txt =~ s/^et\s+//g;
  $txt =~ s/ et( del? )/,\1/g;
  $txt =~ s/\s+,/,/g;
  $txt =~ s/(,\s*,|,+)/,/g;
  $txt =~ s/,+/,/g;
  $txt =~ s/^\s*,\s*//g;
  $txt =~ s/\s*,\s*$//g;
  $txt =~ s/president/président/ig;
  $txt =~ s/([\s,]+)rat*a*ch[eé]*s?/$1rattachés/ig;
  $txt =~ s/([\s,]+)ap*a*rent[eé]*s?/$1apparentés/ig;
  $txt =~ s/([\s,]+)col*[eè]*gues?/$1collègues/ig;
  $txt =~ s/ et /, /;
  return $txt;
}

sub clean_texte {
  my $txt = shift;
  $txt =~ s/<(\/)?(div|span|font|object|h\d+)>/<$1p>/ig;
  $txt =~ s/<(\/)?em>/<$1i>/gi;
  $txt =~ s/<(\/)?strong>/<$1b>/gi;
  $txt =~ s/<!--[^>]*>//g;
  $txt =~ s/<xml>.*<\/xml>//ig;
  $txt =~ s/<style>.*<\/style>//ig;
  $txt =~ s/^(<\/[^>]+>)+//g;
  $txt =~ s/(<\/?p>)+(<\/?t[rdh][^>]*>)/$2/ig;
  $txt =~ s/(<\/?t[rdh][^>]*>)(<\/?p>)+/$1/ig;
  $txt =~ s/\s*(<\/?p>\s*)*(<\/table>)(\s*<\/p>)*\s*/$2/ig;
  $txt =~ s/\s*(<p>\s*)*(<table>)(\s*<\/?p>)*\s*/$2/ig;
  $txt =~ s/<table>\s*(<\/?t[rdh][^>]*>\s*)*<\/table>//ig;
  $txt =~ s/(<\/?p>)(<\/?[^>]+>)+(<\/?p>)/$1$3/ig;
  $txt =~ s/^([^<])/<p>$1/;
  $txt =~ s/([^>])$/$1<\/p>/;
  $txt =~ s/<\/(p|table)>([^<])/<\/$1><p>$2/ig;
  $txt =~ s/([^>])<(p|table)>/$1<\/p><$2>/ig;
  $txt =~ s/\s*<p>(\s*<\/?p>)+\s*/<p>/ig;
  $txt =~ s/\s*(<\/?p>\s*)+<\/p>\s*/<\/p>/ig;
  $txt =~ s/(<\/?p>)(<\/?[^>]+>)+(<\/?p>)/$1$3/ig;
  $txt =~ s/^\s*(<\/?p>\s*)*<table>(\s*<\/?t[rdh][^>]*>)*\s*([^<]+<\/p><p>)/<p>$3/i;
  $txt =~ s/(<\/p><p>[^<]+)\s*(<\/?t[rdh][^>]*>\s*)*<\/table>(\s*<\/?p>)*\s*$/$1<\/p>/i;
  $txt =~ s/<p>$//i;
  $txt =~ s/(<[ubi]>)<p>([^<]+)<\/p>(<\/[ubi]>)/$1$2$3/gi;
  $txt =~ s/<b><\/b>//gi;
  $txt =~ s/<u><\/u>//gi;
  $txt =~ s/<i><\/i>//gi;
  $txt =~ s/(<b>)+/<b>/gi;
  $txt =~ s/(<\/b>)+/<\/b>/gi;
  $txt =~ s/(<u>)+/<u>/gi;
  $txt =~ s/(<\/u>)+/<\/u>/gi;
  $txt =~ s/(<i>)+/<i>/gi;
  $txt =~ s/(<\/i>)+/<\/i>/gi;
  $txt =~ s/<p>\s+/<p>/gi;
  $txt =~ s/\s+<\/p>/<\/p>/gi;
  return $txt;
}

