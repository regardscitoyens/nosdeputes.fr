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
my %sessions;

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
  $sessions{sprintf('%04d%04d', 2000+$3, 2001+$3)}++;
  $doc{'num'} = $5;
  $annexes = $6;
  if ($typeid eq "tas") {
    $doc{'id'} = "TAS".$doc{'id'};
    $type = "Texte adopté";
  } elsif ($typeid eq "motionpjl") {
    $type = "Motion";
  } elsif ($typeid eq "pjl") {
    $type = "Projet de loi";
  } elsif ($typeid eq "ppl") {
    $type = "Proposition de loi";
  } elsif ($typeid eq "ppr") {
    $type = "Proposition de résolution";
  } elsif ($typeid eq "a") {
    $type = "Avis";
  } elsif ($typeid =~ /[lr]/) {
    $type = "Rapport";
  }
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
$tmpdate = $1 if ($string =~ /<meta name="date" content="(\d{4}-\d\d-\d\d)">/i);
$header = $1 if ($string =~ s/^.*<body>(.*)<!--#hr function="section"-->//i);
$string = decode_entities($string);
$string =~ s/(\W)UMP(\W)/$1Union pour un mouvement populaire$2/g;
$reperes = $1 if ($string =~ s/^.*<!--repere-box-->(.*)<!--\/repere-box-->//i);
if ($string =~ s/^(.*)CONSTITUTION DU 4 OCTOBRE 1958//) {
  $sommaire = $1;
} elsif ($string =~ s/^(.*)(<p Align=center>(<[^>]*>)?SESSION (EXTRA)?-?ORDINAIRE DE (\d{4})-(\d{4})<\/)/$2/) {
  $sessions{$5.$6}++;
  $sommaire = $1;
} elsif ($string =~ s/^(.*)(<p Align=center>(<[^>]*>)?SÉNAT<\/)/$2/) {
  $sommaire = $1;
} elsif ($string =~ s/^(.*)<\/ul>\s*<hr\/?>//i) {
  $sommaire = $1;
} elsif ($string =~ s/^(.*)(<p Align=center>(<[^>]*>)?(N°\s*$num)<\/)/$3/i) {
  $sommaire = $1;
} elsif ($string =~ s/^(.*)(<p Align=center>(<[^>]*>)?(PRO(JET|POSITION) DE LOI)<\/)/$3/i) {
  $sommaire = $1;
} elsif ($string =~ s/^(.*)<p Align=center>(<[^>]*>)?_+<\///i) {
  $sommaire = $1;
} elsif ($string =~ s/^(.*)(<[\/uldivbr\s]+>\s*)+<hr>//i) {
  $sommaire = $1;
} else {
  $string =~ s/^(.*)<div id="wysiwyg">//ig;
  $sommaire = $1;
}
$string =~ s/<!--\s*(START : box|END : primary|#\/section|%finContenu)\s*-->.*$//;
$string =~ s/<![^>]*>//g;
if ($header) {
  if ($header =~ /<!--#set var="TITLE" value="([^"]*)"/) {
    $doc{'titre'} = ucfirst(decode_entities($1));
    $doc{'titre'} =~ s/"\s*([^"]*)\s*"/« $1 »/g;
    $doc{'titre'} =~ s/\s*\([^\)]+\)+\s*/ /g;
  }
  if ($header =~ /<!--#set var="BACKURL" value="([^"]*)dossier(leg|-legislatif)\/([^"]*)\.html"/) {
    $doc{'dossier'} = $3;
    $doc{'dossier'} =~ s/^04-323/ppl04-323/;
    $doc{'dossier'} =~ s/^ppl707/ppl10-707/;
  }
  if ($typeid =~ /^[lr]$/ && $header =~ /<!--#set var="A3LIB" value="([^"]*)"/) {
    $type = $1;
    $type =~ s/s / /ig;
    $type =~ s/s$//i;
    $type =~ s/ législatif$//;
  }
  if ($header =~ /<!--#set var="KEYWORDS" value="([^"]*)"/) {
    $doc{'keywords'} = decode_entities($1);
    $doc{'keywords'} =~ s/"//g;
    $doc{'keywords'} =~ s/[ ,]+Sénat.*$//ig;
    $doc{'keywords'} =~ s/[,\s]*\(([^\)]*)\)+\s*/, $1, /g;
    $doc{'keywords'} =~ s/[\(\)]//g;
    $doc{'keywords'} =~ s/\s+et\s+/, /ig;
    $doc{'keywords'} =~ s/\s*-\s*/-/g;
    $doc{'keywords'} =~ s/[ ,]*Sénat.*$//ig;
    $doc{'keywords'} =~ s/([^\s,\-\.A-ZÀÉÈÊÎÏÔÙÇ])([A-ZÀÉÈÊÎÏÔÙÇ]+)/$1, $2/g;
    $doc{'keywords'} = lc($doc{'keywords'});
    $doc{'keywords'} =~ s/([ÀÉÈÊÎÏÔÙÇ])/\L$1/g;
    $doc{'keywords'} =~ s/\s*[,\.:\/\\]+\s*/./g;
    $doc{'keywords'} =~ s/ l\W(.)/ l'$1/g;
    $doc{'keywords'} =~ s/ [dl][uel'as\s]+\././g;
    $doc{'keywords'} =~ s/\.+/./g;
    $doc{'keywords'} =~ s/^\.//g;
    $doc{'keywords'} =~ s/\.$//g;
    $doc{'keywords'} =~ s/\...?\././ig;
  }
  if (!$tmpdate && $header =~ /<!--#set var="DESCRIPTION" value="[^"]* (\d+e?r? \S* \d+)[^"]*"/) {
    $tmpdate = join '-', datize($1);
  }
}

if ($sommaire) {
  if ($typeid eq "motionpjl" && $sommaire =~ />N°\s*(\d+)</) {
    $doc{'id'} = sprintf("%03d", $1);
  }
  $sommaire =~ s/^\s*(<[^>]+>\s*)*<p>/<p>/i;
  $sommaire =~ s/<p>Disponible au.*$//i;
  $sommaire =~ s/<[^>]+>*//g;
  $sommaire =~ s/\s+/ /g;
  $sommaire =~ s/\s*,+\s*/, /g;
  if ($sommaire =~ /(-|le) (\d+e?r? \S* \d+)/i) {
    $doc{'date'} = $2;
    $sessions{sessionize(datize($doc{'date'}))}++;
  }
  $doc{'auteurs'} = $2 if ($sommaire =~ /(de|par) (M[Mlmes\.]\s*.*), déposé/);
  $doc{'auteurs'} =~ s/\s*(fait )?au nom de la commission mixte paritaire//i;
}

if ($reperes) {
  $reperes =~ s/<[^!>]+>*//g;
  $sommaire =~ s/\s+/ /g;
  $sommaire =~ s/\s*,+\s*/, /g;
  if ($reperes =~ /<![\-\s]*START-date[\-\s]*>(\d+e?r? \S* \d+)\s*:?<![\-\s]*END-date[\-\s]*>/i) {
    $sessions{sessionize(datize($1))}++;
    if (!$doc{'date'}) {
      $doc{'date'} = $1;
    }
  }
  if (!$doc{'auteurs'}) {
    $doc{'auteurs'} = $1 if ($reperes =~ /<![\-\s]*START-auteurs[\-\s]*>Par (M[Mlmes\.]\s*.*)<![\-\s]*END-auteurs[\-\s]*>/i);
    if ($reperes =~ /<![\-\s]*START-organismes[\-\s]*>(.*)<![\-\s]*END-organismes[\-\s]*>/i) {
      $com = $1;
      if ($com !~ /mixte paritaire/) {
        $doc{'auteurs'} .= ", $com Auteur";
      }
    }
  }
}
$doc{'auteurs'} =~ s/ et ([^,]+(sénat|député|collègues))/, $1/ig;
$doc{'auteurs'} =~ s/[,\s]*sénat(eur|rice)([^,]*député)?[,\s]*/, /gi;
$doc{'auteurs'} =~ s/au nom (d[elau'\s]+)?//gi;

$string =~ s/<\/?sup>//ig;
$string =~ s/<a[^>]*href=[^>]*>(<[^>]*>)*[^<]*(<[^>]*>)*<\/a>//ig;
$string =~ s/\s*<[^>]*>\s*/ /g;
$string =~ s/\\/-/g;
$string =~ s/"/&quot;/g;
$string =~ s/\s+/ /g;
$string =~ s/\s*'+\s*/'/g;
$string =~ s/\s*,+\s*/, /g;
$string =~ s/^.*au format pdf\s*\([^\)]*\)+\s*//i;

if(!$yml) {
  $doc{'contenu'} = $string;
}

if ($display_text) {
  utf8::encode($string);
  print $string;
  exit;
}

$string =~ s/composition d[eula'\s]+(office|observatoire|(com)?(d[eé]l[eé]gat|miss)ion).*$//i;
$string =~ s/Voir le\(?s?\)? numéro\(?s?\)?.*$//i;
$string =~ s/Mesdames, Messieurs.*$//i;
if ($string =~ s/^.*ORDINAIRE DE (\d{4})-(\d{4})\s*//) {
  $sessions{$1.$2}++;
}
if ($string =~ s/^.*enregistr[eé]+ [aà] la Pr[eé]sidence du S[eé]nat[dule\s]*((\d+e?r? \S* \d+)[dule\s]*)?(\d+e?r? \S* \d+)//i) {
  $doc{'date'} = $3 if (!$doc{'date'});
  $sessions{sessionize(datize($3))}++;
}
if ($string =~ s/^.*annexe au procès-verbal de la séance[dule\s]*((\d+e?r? \S* \d+)[dule\s]*)?( \d+e?r? \S* \d+)//i) {
  $doc{'date'} = $3 if (!$doc{'date'});
  $sessions{sessionize(datize($3))}++;
}

$string =~ s/__+.*$//;
$string =~ s/\s*Série.*$//;
$string =~ s/[,\s]*(PRÉSENTÉE? )?EN APPLICATION DE L'ARTICLE \d+(,? \S+\s?\d*,?)? DU RÈGLEMENT[,\s]*/ /i;
$string =~ s/\s*EXPOSÉ DES MOTIFS.*$//i;
$string =~ s/[cet\s]*(groupe|office|observatoire|(délégat|(com)?miss)ion)[^\.\,]+composée? de.*$//i;
$string =~ s/[\s,\(]*déposée? sur le bureau d.*$//i;
$string =~ s/[\s,\(]*Le Sénat a (adopt|modifi)é.*$//;
$string =~ s/[\s,\(]*Est devenue résolution du Sénat.*$//;
$string =~ s/[\s,\(]*r?envoyée? à la com.*$//i;
$string =~ s/- sur .*[^M]\.//;
$string =~ s/SOMMAIRE.*$//;

#Find tome/volume/annexe number in text or retrieve from url (less sure)
if ($annexes) {
  $doc{'annexe'} = sprintf("t%02d", deromanize($2)) if ($string =~ /T(OME|ome)[N\s°]+([\dIVX]+)/);
  $doc{'annexe'} .= sprintf("v%02d", $2) if ($string =~ /F(ASCICULE|ascicule)[N\s°]+([\dIVX]+)/);
  if ($string =~ /V(olume|OLUME)[N\s°]+([\dIVX]+)/) {
    $tmpann = sprintf("%02d", $2);
    $tmpann = ($doc{'annexe'} =~ /v/ ? "a" : "v").$tmpann;
    $doc{'annexe'} .= $tmpann;
  }
  $doc{'annexe'} .= sprintf("a%02d", $2) if ($string =~ /A(NNEXE|nnexe)[N\s°]+([\dIVX]+)/);
  $string =~ s/T(OME|ome)[N\s°]+([\dIVX]+).* [pP](ar|ar) / Par /;
  if (!$doc{'annexe'} && $annexes =~ /^-(\d)(-(\d+))?(-?(\d+))?/) {
    $doc{'annexe'} = sprintf("t%02d", $1);
    $doc{'annexe'} .= sprintf("v%02d", $3) if ($3);
    $doc{'annexe'} .= sprintf("a%02d", $5) if ($5);
  }
}

# Find auteurs from header loi
$tmpauteurs = $doc{'auteurs'};
$tmpauteurs =~ s/\s*,.*$//;
$tmpauteurs =~ s/[\(\)]//g;
$tmpauteurs =~ s/^\s*(M[Mlmes\.]+)\s*//;
$autsexe = $1;
$tmpstring = $string;
$tmpstring =~ s/^.*([Mlmes\.\s]+)*$tmpauteurs/Par $autsexe $tmpauteurs/i if ($tmpauteurs);
$tmpstring =~ s/[pP]?(R[EÉ]SENT[EÉ]|r[eé]sent[eé])?[eE]?\s*(D[Ee]|[pP][aA][Rr])[-\s]+(M[Mlmes\.\s]+[A-ZÀÉÈÊÎÏÔÙÇ].*), [dD]éputé[\s\.,]*//;
if ($tmpstring =~ s/[rR]apporteur[es]* [sS]pécia[leuxs]+ : (M[Mlmes\.\s]+.*) \(1\).*$//) {
  $auteurs = $1;
} elsif ($tmpstring =~ s/[\.,\s]*[pP]?(R[EÉ]SENT[EÉ]|r[eé]sent[eé])?[eE]?\s*[pP][aA][Rr][\-\s]+((M[Mlmes\.\s]+)[A-ZÀÉÈÊÎÏÔÙÇ].*),? [sS]énat(eur|rice).*$//) {
  $auteurs = $2;
} elsif ($tmpstring =~ s/[\.,\s]*[pP]?(R[EÉ]SENT[EÉ]|r[eé]sent[eé])?[eE]?\s*[pP][aA][Rr][\-\s]+((M[Mlmes\.\s]+)[A-ZÀÉÈÊÎÏÔÙÇ].*),? [pP]résident.*$//) {
  $auteurs = $2;
} elsif ($tmpstring =~ s/[\.,\s]*[pPF]?(R[EÉ]SENT[EÉ]|r[eé]sent[eé]|ait|AIT)?[eE]?\s*[pP][aA][Rr][\-\s]+((M[Mlmes\.\s]+)[A-ZÀÉÈÊÎÏÔÙÇ].*)\s*$//) {
  $auteurs = $2;
} elsif ($tmpstring =~ s/[\.,\s]*[pPF](R[EÉ]SENT[EÉ]|r[eé]sent[eé]|ait|AIT)[eE]?\s*[pP][aA][Rr][\-\s]+([A-ZÀÉÈÊÎÏÔÙÇ]\w\w+.*)\s*$//) {
  $auteurs = $2;
}
$auteurs =~ s/\s*\([^\)]*\)//g;
$auteurs =~ s/[\s,\-\.]+(M[Mlmes\.\s]+)/, $1/g;
$auteurs =~ s/, (rapport|général|sénat|présid)[^,]*//ig;
$auteurs =~ s/,? (est|ce peuple)[,\s].*$//i;
if ($auteurs && (!$doc{'auteurs'} || $type !~ /^(Rapport|Avis)/ || $annexes || $doc{'auteurs'} =~ /collègues/i)) {
  $doc{'auteurs'} = $auteurs;
}
if ($auteurs) {
  $auteurs =~ s/^([^,]+),.*$/$1/;
  $string =~ s/[\.,\s]*(pr[eé]sent[eé]|fait)?e?\s*par[\-\s]+$auteurs(,? ?(président|sénat(eur|rice)))?.*$//;
}

$doc{'auteurs'} =~ s/, (député|rapporteur)//gi;
if ($string =~ s/[,\s]*TEXTE [EÉ]LABOR[EÉ] PAR LA COMMISSION MIXTE PARITAIRE.*$//) {
  $doc{'type'} = "Texte de la commission mixte paritaire";
} elsif ($string =~ s/[,\s]*TEXTE DE LA (COMMISSION[^\(]+)\(1\).*$//) {
  $doc{'type'} = "Texte de la commission";
  $doc{'auteurs'} = "";
} elsif ($string =~ s/^.*[,\s]*(présenté|fait)? ?(au nom (d['elua\s]+)?((groupe|observatoir|office|(com)?(delegat|miss)ion)[^\(]*))\(1\)/$2/i) {
  $doc{'type'} = $type;
  $doc{'auteurs'} .= ", ".ucfirst(lc($4))." Auteur" if (lc($4) !~ /commission mixte paritaire/i && $doc{'auteurs'} !~ /groupe|observatoir|office|mission/);
} else {
  $doc{'type'} = $type;
}

$string =~ s/en application de .* (constitution|règlement), //i;
$string =~ s/[\s,]*TRANSMISE? PAR.*$//i;
$string =~ s/[\s,]*présentée? au nom de (M[Mlmes\.\s]+.*).*$//i;
$string =~ s/[\s,\.]* (par|de) (M[Mlmes\.\s]+.*).*$//i;
$string =~ s/^\s+//;
if ($doc{'type'} eq "Motion" || !$doc{'titre'} || $doc{'titre'} eq $type) {
  $doc{'titre'} = ucfirst(lc($string));
  $doc{'titre'} =~ s/\s*présentée?\s*$//;
  $doc{'titre'} =~ s/\s*\([^\)]+\)+[,\s]*//g;
  $string = "";
}
$doc{'titre'} =~ s/\s*$type[,\s]*//;
$string =~ s/\s+sur (: )?-.*$//;
if ($doc{'type'} =~ /^(Avis|Rapport|Texte)/) {
  if ($doc{'titre'} =~ s/\s*P(ro(jet|position) de (loi|résolution)( européenne)?)\s*//) {
    $tmpstring = " sur l".($2 eq "jet" ? "e" : "a")." p$1";
    if ($string !~ / (en discussion d|sur)[ulea\s]+pro(jet|position)/i && $string !~ /^\s*pro(jet|position)/i) {
      $string =~ s/^résolution//i;
      $string .= $tmpstring;
    }
  }
} else {
  $doc{'titre'} = lcfirst($doc{'titre'});
}
$doc{'titre'} =~ s/en application de .* (constitution|règlement), //i;
$doc{'titre'} =~ s/[\s,]*présentée?[\s,]*//gi;
$doc{'titre'} =~ s/[\.,\s]+$//;
$string = lc($string);
$type = lc($doc{'type'});
$string =~ s/\s*$type[,\s]*//;
$string = "organique ".$string if ($doc{'titre'} =~ s/^\s*organique\s*//);
$string = "ratifiant ".$string if ($doc{'titre'} =~ s/^\s*organique\s*//);
$tmp = lc($doc{'titre'});
$string =~ s/[,\s]*$tmp[,\s\?!:]*/ /g;
$tmp =~ s/ : .*$//g;
$string =~ s/[,\s]*$tmp[,\s\?!:]*/ /g;
if ($typeid =~ /^([alr]|ga|rap)$/) {
  $string =~ s/rapport.*(sur )?/$1/i;
}

$doc{'type_details'} = $string if (length($string) <= 500);
if ($doc{'type'} =~ /^Texte/) {
  $doc{'type_details'} =~ s/(sur la )?propo/sur la propo/;
  $doc{'type_details'} =~ s/(sur le )?projet/sur le projet/;
  $doc{'type_details'} =~ s/(sur la proposition de )?résolu/sur la proposition de résolu/;
  $doc{'type_details'} =~ s/\s*modifié par le sénat//;
}
$doc{'type_details'} =~ s/\s*\([^\)]+\)+\s*/ /g;
$doc{'type_details'} =~ s/^[\.,\s]+//;
$doc{'type_details'} =~ s/\W+$//;
$doc{'type_details'} =~ s/\s+/ /g;
$doc{'type_details'} =~ s/^groupe/du groupe/;
if ($doc{'type_details'} =~ s/^office parlementaire //i && $doc{'type'} =~ /^Rapport/) {
  $doc{'type'} = "Rapport d'office parlementaire";
}
$doc{'type_details'} =~ s/^((com)?(miss|délégat)ion)/de la $1/;
$doc{'type_details'} =~ s/assemblée/Assemblée/;
$doc{'type_details'} =~ s/[\s,]*adressé à M. le président du Sénat//i;
$doc{'type_details'} =~ s/sénat/Sénat/g;
$doc{'type_details'} =~ s/france/France/g;
$doc{'titre'} =~ s/france/France/g;
if ($doc{'type_details'} =~ /^\s*tableau comparatif/i) {
  $doc{'type_details'} = $doc{'auteurs'};
  $doc{'type_details'} =~ s/^.* au nom /au nom /;
  $doc{'type_details'} .= $tmpstring;
}
$doc{'type_details'} =~ s/^\s*fait\s*//i;
$doc{'type_details'} =~ s/^compte/ - compte/i;
$doc{'type_details'} =~ s/[\s,]*(après eng|sur l|au nom d|en [\d\wè]+ lecture|adopté)/, $1/ig;
$doc{'type_details'} =~ s/[\(\)]//g;
$doc{'type_details'} =~ s/\s*compte[-\s]*rendu du déplacement.*$//i;
$doc{'type_details'} =~ s/^.*projet de loi.*retiré.*par le.*ministre.*$//i;
$doc{'type_details'} =~ s/^.*dépôt.*publié.*journal officiel.*//i;
$doc{'type_details'} =~ s/\s*présentée?[\s,]*$//;
$doc{'type_details'} =~ s/^[\s,]*présentée?\s*//;
$tmpdetails = $doc{'type_details'};
if ($doc{'titre'} =~ /$tmpdetails/) {
  $doc{'type_details'} = "";
}
if ((substr $doc{'type_details'}, 0, 30) eq (substr $doc{'titre'}, 0, 30)) {
  $doc{'titre'} = (length($doc{'titre'}) > length($doc{'type_details'}) ? $doc{'titre'} : $doc{'type_details'});
  $doc{'type_details'} = "";
}
$doc{'type_details'} =~ s/(\s+[ld])\s*$/$1'/i;
if ($doc{'type_details'} =~ s/^lettre\s*(.*)$/$1/i) {
  $doc{'type_details'} .= " ".lc($doc{'type'});
  $doc{'type'} = "Lettre";
}
if ($doc{'type_details'} =~ /^et /) {
  $doc{'titre'} .= " ".$doc{'type_details'};
  $doc{'type_details'} = "";
}
if (!$doc{'titre'} || $doc{'titre'} =~ /^\s*$/) {
  $doc{'titre'} = $doc{'type_details'};
  $doc{'type_details'} = "";
}
$doc{'titre'} =~ s/[\s,\.]+$//;

#format date
$doc{'date'} = join '-', datize($doc{'date'});
$doc{'date'} = $tmpdate if ($tmpdate && !$doc{'date'});
#reset ID from session in date plus sur
if ($typeid ne "ga") {
  $maxses=0;
  foreach $ses (keys %sessions) {
    if ($sessions{"$ses"} > $maxses) {
      $session = $ses;
      $maxses = $sessions{"$ses"};
    }
  }
  $doc{'id'} = $session."-".$doc{'id'};
}

#clean auteurs
$doc{'auteurs'} =~ s/\s+/ /g;
$doc{'auteurs'} =~ s/[\s,]+(fait )?au nom d[ela'u\s]+(.)/, \U$2/ig;
$doc{'auteurs'} =~ s/[\s,]+fait (.)/, \U$1/ig;
$doc{'auteurs'} =~ s/ et[\s,]*([A-ZÀÉÈÊÎÏÔÙÇ])/, $1/g;
$doc{'auteurs'} =~ s/[\s,]+et les membres/, les membres/g;
$doc{'auteurs'} =~ s/, (premier|ministre|garde|haut|secr)/ \U$1/ig;
$doc{'auteurs'} =~ s/, président[^,]*, /, /ig;
$doc{'auteurs'} =~ s/^[,\s]+//;
$doc{'auteurs'} =~ s/[,\s]*$/,/;
$doc{'auteurs'} =~ s/\s*\([^\)]*\)+\s*/ /g;
$doc{'auteurs'} =~ s/[\(\)]//g;
$doc{'auteurs'} =~ s/de l'union centr/Union centr/ig;
#$doc{'auteurs'} =~ s/ {1,2,3} 

if ($doc{'type'} =~ /^(Avis|Rapport)/) {
  if ($doc{'auteurs'} =~ /mission|observatoire|office|délégation/i && $doc{'auteurs'} !~ /Auteur/) {
    $doc{'auteurs'} =~ s/,$/ Auteur,/;
  }
  $doc{'auteurs'} =~ s/ ([A-ZÀÉÈÊÎÏÔÙÇ][^,\s]*),/ $1 Rapporteur,/g;
} else {
  $doc{'auteurs'} =~ s/ ([A-ZÀÉÈÊÎÏÔÙÇ][^,\s]*),/ $1 Auteur,/g;
  if ($doc{'auteurs'} ne ',' && $doc{'auteurs'} !~ /Auteur,$/) {
    $doc{'auteurs'} =~ s/[\s,]*$/ Auteur/;
  }
}
$doc{'auteurs'} =~ s/Auteur (Au|Rappor)teur/Auteur/;
$doc{'auteurs'} =~ s/\s*,\s*/, /g;

#propage le sexe et identifie les cosignataires via l'ordre
$auteurs = "";
$sexe = "Mme ";
$prevaut = "AAA";
$cosign = 0;
$auteurs = $doc{'auteurs'};
while ($doc{'auteurs'} =~ /\s*([^,]* )([A-ZÀÉÈÊÎÏÔÙÇ][^,\s]*) (Rapporteur|Auteur),/g) {
  $aut = $2;
  $name = $1.$aut;
  $fct = $3;
  next if ($aut =~ /mission|délégation|office|ministre/);
  $sexename = "";
  $sexename = $1 if ($name =~ s/^(M[Mlmes\.]+\s)//);
  $prevsexe = $sexe;
  if ($sexename =~ /[le]/) {
    $sexe = "Mme ";
  } elsif ($sexename =~ /^M[\.Mms]+/) {
    $sexe = "M. ";
  }
  $aut =~ s/[àéèêëïîôùüû]//gi;
  if (!$cosign && $fct eq "Auteur" && $doc{'type'} =~ /^(Propo|Motion)/ && ( ($sexe eq $prevsexe && $aut lt $prevaut) || ($sexe ne $prevsexe && $sexe eq "Mme ") )) {
    $cosign = 1;
  }
  $prevaut = $aut;
  $fct = "Cosignataire" if ($cosign);
  $auteurs =~ s/$sexename$name (Rapporteur|Auteur)/$sexe$name $fct/;
}
$doc{'auteurs'} = $auteurs;
$doc{'auteurs'} =~ s/[,\s]+(les membres|et) du groupe (du )?(.)/, \U$3/ig;
$doc{'auteurs'} =~ s/,[,\s]+/, /g;
$doc{'auteurs'} =~ s/[,\s]+$//;
$doc{'auteurs'} =~ s/([^M])\./$1/g;
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

print '{"source": "'.$doc{'source'}.'", "id": "'.$doc{'id'}.'", "numero": "'.$doc{'num'}.'", "annexe": "'.$doc{'annexe'}.'", "date": "'.$doc{'date'}.'", "auteurs": "'.$doc{'auteurs'}.'", "dossier": "'.$doc{'dossier'}.'", "type": "'.$doc{'type'}.'", "type_details": "'.$doc{'type_details'}.'", "titre": "'.$doc{'titre'}.'", "motscles": "'.$doc{'keywords'}.'", "contenu": "'.$doc{'contenu'}.'"}'."\n";

