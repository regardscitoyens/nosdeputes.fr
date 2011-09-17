#!/usr/bin/perl

use HTML::Entities;
use URI::Escape;
use Encode;
use utf8;
require "../common/common.pm";

$file = shift;
$yml = shift;
$display_text = shift;

my %doc;
$doc{'source'} = $file;
$doc{'source'} =~ s/^[^\/]+\///;
$doc{'source'} = uri_unescape($doc{'source'});
$doc{'id'} = $doc{'source'};
$doc{'id'} =~ s/^http\:\/\/.*\/((motion)?p*j?t?g?[alr]s?)(\d{2,3})-?((\d{3})([\-\d]*))?(_mono)?\.html$/$4/i;
$typeid = lc($1);
if ($typeid eq "ga") {
  $doc{'num'} = sprintf('%03d', $3);
  $doc{'id'} = "GA".$doc{'num'};
  $type = "Rapport de groupe d'amitié";
} else {
  $session = sprintf('%04d%04d', 2000+$3, 2001+$3);
  $doc{'num'} = $5;
  $annexes = $6;
  if ($typeid eq "tas") {
    $doc{'id'} = "TAS".$doc{'id'};
    $type = "Texte adopté";
  } elsif ($typeid =~ /pjl/) {
    $type = "Projet de loi";
  } elsif ($typeid eq "ppl") {
    $type = "Proposition de loi";
  } elsif ($typeid eq "ppr") {
    $type = "Proposition de résolution";
  } elsif ($typeid eq "a") {
    $type = "Avis";
  } elsif ($typeid eq "l") {
    $type = "Rapport législatif";
  } elsif ($typeid eq "r") {
    $type = "Rapport";
  }
  $doc{'id'} = $session."-".$doc{'id'};
}
$num = $doc{'num'};
$num =~ s/^0+//;

open(FILE, $file);
@string = <FILE>;
$string = "@string";
close FILE;
utf8::decode($string);
$string =~ s/\r//g;
$string =~ s/\s+/ /g;
$string =~ s/<![\s\-]*\[if[^\]]+\][\s\-]*>//ig;
$string =~ s/<![\s\-]*\[endif\][\s\-]*>//ig;
$string =~ s/<![\s\-]*\/\*[^\>]*>//g;
$string =~ s/’/'/g;
$string =~ s/\s*<a name=[^>]+>\s*(<\/a>)?\s*//ig;
$header = $1 if ($string =~ s/^.*<body>(.*)<!--#hr function="section"-->//i);
$string = decode_entities($string);
$reperes = $1 if ($string =~ s/^.*<!--repere-box-->(.*)<!--\/repere-box-->//i);
if ($string =~ s/^(.*)(<p Align=center>(<[^>]*>)?(S[EÉ]NAT|SESSION (EXTRA)?-?ORDINAIRE DE \d{4}-\d{4})(<\/[^>]*>)?<\/p>)/$2/i) {
  $sommaire = $1;
} elsif ($string =~ s/^(.*)<\/ul>\s*<hr\/?>//i) {
  $sommaire = $1;
} elsif ($string =~ s/^(.*)<p Align=center>(<[^>]*>)?_+(<\/[^>]*>)?<\/p>//i) {
  $sommaire = $1;
} elsif ($string =~ s/^(.*)(<p Align=center>(<[^>]*>)?(N°\s*$num)(<\/[^>]*>)?<\/p>)/$2/i) {
  $sommaire = $1;
} else {
  $string =~ s/^(.*)(<[\/uldivbr\s]+>\s*)+<hr>//i;
  $sommaire = $1;
}
$string =~ s/<!--\s*(START : box|END : primary|#\/section|%finContenu)\s*-->.*$//;
$string =~ s/<![^>]*>//g;

#$string =~ s/<[\/ ]*br[\/ ]*>//gi;
#$string =~ s/\s*(<\/?hr\/?>)\s*/\n<hr\/>/ig;

if ($header) {
  if ($header =~ /<!--#set var="TITLE" value="([^"]*)"/) {
    $doc{'titre'} = ucfirst(decode_entities($1));
    $doc{'titre'} =~ s/"\s*([^"]*)\s*"/« $1 »/g;
    $doc{'titre'} =~ s/\s*\([^\)]+\)\s*/ /g;
  }
  if ($header =~ /<!--#set var="BACKURL" value="([^"]*)dossier(leg|-legislatif)\/([^"]*)\.html"/) {
    $doc{'dossier'} = $3;
  }
  if ($typeid =~ /^[lr]$/ && $header =~ /<!--#set var="A3LIB" value="([^"]*)"/) {
    $type = $1;
    $type =~ s/s / /ig;
    $type =~ s/s$//i;
  }
  if ($header =~ /<!--#set var="KEYWORDS" value="([^"]*)"/) {
    $doc{'keywords'} = decode_entities($1);
    $doc{'keywords'} =~ s/"//g;
    $doc{'keywords'} =~ s/[ ,]+Sénat.*$//ig;
    $doc{'keywords'} =~ s/[,\s]*\(([^\)]*)\)\s*/, $1, /g;
    $doc{'keywords'} =~ s/\s+et\s+/, /ig;
    $doc{'keywords'} =~ s/\s*-\s*/-/g;
    $doc{'keywords'} =~ s/[ ,]*Sénat.*$//ig;
    $doc{'keywords'} =~ s/([^\s,-\.A-ZÀÉÈÊÎÏÔÙÇ])([A-ZÀÉÈÊÎÏÔÙÇ]+)/$1, $2/g;
    $doc{'keywords'} = lc($doc{'keywords'});   
    $doc{'keywords'} =~ s/([ÀÉÈÊÎÏÔÙÇ])/\L$1/g;
    $doc{'keywords'} =~ s/\s*,+\s*/./g;
    $doc{'keywords'} =~ s/^[\.\s]+//g;
    $doc{'keywords'} =~ s/[\.\s]+$//g;
  }
}

if ($sommaire) {
  $sommaire =~ s/^\s*(<[^>]+>\s*)*<p>/<p>/i;
  $sommaire =~ s/<p>Disponible au.*$//i;
  $sommaire =~ s/<[^>]+>*//g;
  $sommaire =~ s/\s+/ /g;
  $sommaire =~ s/\s*,+\s*/, /g;
  $doc{'date'} = $2 if ($sommaire =~ /(-|le) (\d+e?r? \S* \d+)/i);
  $doc{'auteurs'} = $2 if ($sommaire =~ /(de|par) (M[Mlmes\.]\s*.*), déposé/);
  $doc{'auteurs'} =~ s/\s*(fait )?au nom de la commission mixte paritaire//i;
}

if ($reperes && !($doc{'date'} || $doc{'auteurs'})) {
  $reperes =~ s/<[^!>]+>*//g;
  $sommaire =~ s/\s+/ /g;
  $sommaire =~ s/\s*,+\s*/, /g;
  $doc{'date'} = $1 if ($reperes =~ /<![\-\s]*START-date[\-\s]*>(\d+e?r? \S* \d+)\s*:?<![\-\s]*END-date[\-\s]*>/i);
  $doc{'auteurs'} = $1 if ($reperes =~ /<![\-\s]*START-auteurs[\-\s]*>Par (M[Mlmes\.]\s*.*)<![\-\s]*END-auteurs[\-\s]*>/i);
  if ($reperes =~ /<![\-\s]*START-organismes[\-\s]*>(.*)<![\-\s]*END-organismes[\-\s]*>/i) {
    $com = $1;
    if ($com !~ /mixte paritaire/) {
      $doc{'auteurs'} .= ", $com Auteur";
    }
  }
}

$string =~ s/<\/?sup>//ig;
$string =~ s/\s*<[^>]*>\s*/ /g;
$string =~ s/\\/-/g;
$string =~ s/"/&quot;/g;
$string =~ s/\s+/ /g;
$string =~ s/\s*'+\s*/'/g;
$string =~ s/\s*,+\s*/, /g;
$string =~ s/^.*au format pdf\s*\([^\)]*\)\s*//i;

#$doc{'contenu'} = $string;

$string =~ s/Voir le\(?s?\)? numéro\(?s?\)?.*$//i;

if ($display_text) {
  utf8::encode($string);
  print $string;
  exit;
}

$string =~ s/^.*ORDINAIRE DE \d{4}-\d{4}\s*//;
if ($string =~ s/^.*Enregistr[eé] [aà] la Pr[eé]sidence du S[eé]nat le (\d+e?r? \S* \d+)//i) {
  $doc{'date'} = $1;
}
if ($string =~ s/^.*Annexe au procès-verbal de la séance du (\d+e?r? \S* \d+)//i) {
  $doc{'date'} = $1;
}

$string =~ s/__+.*$//;
$string =~ s/\s*Série.*$//;
$string =~ s/[,\s]*(PRÉSENTÉE? )?EN APPLICATION DE .* RÈGLEMENT[,\s]*/ /i;
$string =~ s/\s*EXPOSÉ DES MOTIFS.*$//i;
if ($annexes) {
  $doc{'annexe'} = sprintf("t%02d", deromanize($2)) if ($string =~ /T(OME|ome)[N\s°]+([\dIVX]+)/);
  $doc{'annexe'} .= sprintf("v%02d", $2) if ($string =~ /V(OLUME|olume)[N\s°]+([\dIVX]+)/);
  $doc{'annexe'} .= sprintf("a%02d", $2) if ($string =~ /A(NNEXE|nnexe)[N\s°]+([\dIVX]+)/);
  $string =~ s/T(OME|ome)[N\s°]+([\dIVX]+).* [pP](ar|ar) / Par /;
  if (!$doc{'annexe'} && $annexes =~ /^-(\d)(-(\d+))?(-?(\d+))?/) {
    $doc{'annexe'} = sprintf("t%02d", $1);
    $doc{'annexe'} .= sprintf("v%02d", $3) if ($3);
    $doc{'annexe'} .= sprintf("a%02d", $5) if ($5);
  }
}
if ($string =~ s/[rR]apporteur[es]* [sS]pécia[leuxs]+ : (M[Mlmes\.\s]+.*) \(1\).*$//) {
  $doc{'auteurs'} = $1;
} elsif ($string =~ s/[pP]?(R[EÉ]SENT[EÉ]|r[eé]sent[eé])?[eE]?\s*([dD]e|[pP]ar) (M[Mlmes\.\s]+.*), Sénat(eur|rice).*$//) {
  $doc{'auteurs'} = $3;
} elsif ($string =~ s/[pP]?(R[EÉ]SENT[EÉ]|r[eé]sent[eé])?[eE]?\s*([dD]e|[pP]ar) (M[Mlmes\.\s]+.*), Président.*$//) {
  $doc{'auteurs'} = $3;
}
if ($string =~ s/[,\s]*TEXTE [EÉ]LABOR[EÉ] PAR LA COMMISSION MIXTE PARITAIRE.*$//) {
  $doc{'type'} = "Texte de la commission mixte paritaire";
} elsif ($string =~ s/[,\s]*TEXTE DE LA (COMMISSION[^\(]+)\(1\).*$//) {
  $doc{'type'} = "Texte de la commission";
  $doc{'auteurs'} = "";
} elsif ($string =~ s/^.*[,\s]*(présenté|fait) (au nom de la (commission[^\(]+))\(1\)/$2/i) {
  $doc{'type'} = $type;
  $doc{'auteurs'} .= ", ".ucfirst(lc($3))." Auteur" if (lc($3) ne "commission mixte paritaire ");
} else {
  $doc{'type'} = $type;
}
$string =~ s/[\s,]*Le Sénat a (adopt|modifi)é.*$//;
$doc{'titre'} =~ s/de en application de .* règlement, //i;
$string =~ s/[\s,]*Est devenue résolution du Sénat.*$//;
$string =~ s/[\s,]*TRANSMISE? PAR.*$//i;
$string =~ s/[\s,\.]* par (M[Mlmes\.\s]+.*).*$//i;
$doc{'titre'} =~ s/\s*$type[,\s]*//;
$string =~ s/\s+sur : -.*$//;
if ($doc{'type'} =~ /^(Avis|Rapport|Texte)/) {
  if ($doc{'titre'} =~ s/\s*P(ro(jet|position) de (loi|résolution)( européenne)?)\s*//) {
    $tmpstring = " sur l".($2 eq "jet" ? "e" : "a")." p$1";
    if ($string !~ / (en discussion d|sur)[ulea\s]+pro(jet|position)/i && $string !~ /^\s*pro(jet|position)/i) {
      $string .= $tmpstring;
    }
  }
} else {
  $doc{'titre'} = lcfirst($doc{'titre'});
}
$doc{'titre'} =~ s/en application de .* règlement, //i;
$string = lc($string);
$type = lc($doc{'type'});
$string =~ s/\s*$type[,\s]*//;
$tmp = lc($doc{'titre'});
$string =~ s/[,\s]*$tmp[,\s\?!:]*/ /g;
$tmp =~ s/ : .*$//g;
$string =~ s/[,\s]*$tmp[,\s\?!:]*/ /g;
if ($typeid =~ /^([alr]|ga|rap)$/) {
  $string =~ s/rapport.*(sur )?/$1/i;
}

$doc{'type_details'} = $string if (length($string) <= 500);
if ($doc{'type'} =~ /^Texte/) {
  $doc{'type_details'} =~ s/\s*propo/sur la propo/;
  $doc{'type_details'} =~ s/\s*projet/sur le projet/;
  $doc{'type_details'} =~ s/\s*résolu/sur la proposition de résolu/;
  $doc{'type_details'} =~ s/\s*modifié par le sénat//;
}
$doc{'type_details'} = "organique ".$doc{'type_details'} if ($doc{'titre'} =~ s/^\s*organique\s*//);
$doc{'type_details'} =~ s/\s*\([^\)]+\)\s*/ /g;
$doc{'type_details'} =~ s/^[\.,\s]+//;
$doc{'type_details'} =~ s/[\.,\s]+$//;
$doc{'type_details'} =~ s/\s+/ /g;
$doc{'type_details'} =~ s/^groupe/du groupe/;
$doc{'type_details'} =~ s/assemblée/Assemblée/;
$doc{'type_details'} =~ s/sénat/Sénat/;
if ($doc{'type_details'} =~ /^\s*tableau comparatif/i) {
  $doc{'type_details'} = $doc{'auteurs'};
  $doc{'type_details'} =~ s/^.* au nom /au nom /;
  $doc{'type_details'} .= $tmpstring;
}

#format date
$doc{'date'} = join '-', datize($doc{'date'});
#clean auteurs
$doc{'auteurs'} =~ s/\s+/ /g;
$doc{'auteurs'} =~ s/[\s,]+(fait )?au nom de la\s*(.)/, \U$2/ig;
$doc{'auteurs'} =~ s/ et /, /ig;
$doc{'auteurs'} =~ s/, (premier|ministre|haut|secr)/ $1/ig;
$doc{'auteurs'} =~ s/, Président[^,]*,/,/;
$doc{'auteurs'} =~ s/^[,\s]+//;
$doc{'auteurs'} =~ s/[,\s]*$/,/;
if ($doc{'type'} =~ /^(Avis|Rapport)/) {
  if ($doc{'auteurs'} =~ /commission/i && $doc{'auteurs'} !~ /Auteur/) {
    $doc{'auteurs'} =~ s/,$/ Auteur,/;
  }
  $doc{'auteurs'} =~ s/ ([A-ZÀÉÈÊÎÏÔÙÇ][^,\s]*),/ $1 Rapporteur,/g;
  $doc{'auteurs'} =~ s/Auteur Rapporteur/Auteur/;
} else {
  $doc{'auteurs'} =~ s/ ([A-ZÀÉÈÊÎÏÔÙÇ][^,\s]*),/ $1 Auteur,/g;
  if ($doc{'auteurs'} ne ',' && $doc{'auteurs'} !~ /Auteur,$/) {
    $doc{'auteurs'} =~ s/[\s,]*$/ Auteur/;
  }
}
$doc{'auteurs'} =~ s/[,\s]*les membres du groupe (.)/, \U$1/;
#propage le sexe et identifie les cosignataires via l'ordre
$auteurs = "";
$sexe = "Mme ";
$prevaut = "AAA";
$cosign = 0;
$auteurs = $doc{'auteurs'};
while ($doc{'auteurs'} =~ /\s*([^,]* )([A-ZÀÉÈÊÎÏÔÙÇ][^,\s]*) (Rapporteur|Auteur),/g) {
  $aut = $2;
  $name = $1.$aut;
  $sexename = "";
  $fct = $3;
  $sexename = $1 if ($name =~ s/^(M[Mlmes\.]+\s)//);
  $prevsexe = $sexe;
  if ($sexename =~ /[le]/) {
    $sexe = "Mme ";
  } elsif ($sexename =~ /^M[\.Mms]+/) {
    $sexe = "M. ";
  }
  $aut =~ s/[àéèêëïîôùüû]//gi;
  if (!$cosign && $fct eq "Auteur" && $doc{'type'} =~ /^Propo/ && ( ($sexe eq $prevsexe && $aut lt $prevaut) || ($sexe ne $prevsexe && $sexe eq "Mme ") )) {
    $cosign = 1;
  }
  $prevaut = $aut;
  $fct = "Cosignataire" if ($cosign);
  $auteurs =~ s/$sexename$name (Rapporteur|Auteur)/$sexe$name $fct/;
}
$doc{'auteurs'} = $auteurs;
$doc{'auteurs'} =~ s/[,\s]+$//;
$doc{'auteurs'} = name_lowerize($doc{'auteurs'},1);

foreach $k (keys %doc) {
  utf8::encode($doc{$k});
}

if ($yml) {
  print "\ndocument_".$doc{'id'}.":\n";
  foreach $k (keys %doc) {
    print "  ".lc($k).": ".$doc{$k}."\n";
  }
  exit;
}

print '{"source": "'.$doc{'source'}.'", "id": "'.$doc{'id'}.'", "numero": "'.$doc{'num'}.'", "annexe": "'.$doc{'annexe'}.'", "date": "'.$doc{'date'}.'", "auteurs": "'.$doc{'auteurs'}.'", "dossier": "'.$doc{'dossier'}.'", "type": "'.$doc{'type'}.'", "type_details": "'.$doc{'type_details'}.'", "titre": "'.$doc{'titre'}.'", "motscles": "'.$doc{'keywords'}.'", "contenu": "'.$string.'"}'."\n";

