#!/usr/bin/perl

$dir = $file = $source = shift;
$dir =~ s/^([^\/]+)\/.*$/\1/;
$source =~ s/^[^\/]+\///;
$source =~ s/http(.?)-/http\1:/;
$source =~ s/_/\//g;
$id = $source;
$plflettre = '';
if ($id =~ /plf\d{4}\/([a-z])\d{4}/i) {
  $plflettre = uc($1);
}
$id =~ s/^http\:\/\/.*(\d{4})(-[at].*)?\.asp$/\1$plflettre\2/i;
$id =~ s/^0+//;
$num = $annexe = $id;
$num =~ s/^(\d+)([^\d].*)?$/\1/i;
$annexe =~ s/^\d+([^\d].*)?$/\1/i;
$annexe =~ s/-//g;
if ($annexe =~ /t\d([av]\d+)?$/) {
  $annexe =~ s/t/t0/;
}
if ($annexe =~ /v\d$/) {
  $annexe =~ s/v/v0/;
}
if ($annexe =~ /a\d$/) {
  $annexe =~ s/a/a0/;
}
if ($annexe =~ /t([IVX]+)([av].*)?$/) {
  $ro = $1;
  $rom{'I'} = '01';
  $rom{'II'} = '02';
  $rom{'III'} = '03';
  $rom{'IV'} = '04';
  $rom{'V'} = '05';
  $rom{'VI'} = '06';
  $rom{'VII'} = '07';
  $rom{'VIII'} = '08';
  $rom{'IX'} = '09';
  $rom{'X'} = '10';
  $rom{'XI'} = '11';
  $rom{'XII'} = '12';
  $rom{'XIII'} = '13';
  $rom{'XIV'} = '14';
  $rom{'XV'} = '15';
  $rom{'XVI'} = '16';
  $rom{'XVII'} = '17';
  $rom{'XVIII'} = '18';
  $rom{'XVIII'} = '19';
  $rom{'XVIII'} = '20';
  $ro = $rom{$ro};
  $annexe =~ s/t([IVX]+)([a-v].*)?$/t$ro\2/;
}

open(FILE, $file) ;
@string = <FILE>;
close FILE;

$string = "@string";
$string =~ s/<br>\s*\n//gi;
$string =~ s/&#339;|œ+/oe/g;
$string =~ s/&#8217;/'/g;
$string =~ s/&#8211;/-/g;
$string =~ s/&#8230;/\.\.\./g;
$string =~ s/<\/?u>//gi;
$string =~ s/<\/?sup>//gi;
$string =~ s/<\/?span( style=[^>]+)?>//gi;
$string =~ s/<!\-\-\w*\-\->//ig;
$string =~ s/\r//g;
$string =~ s/&nbsp;/ /gi;
$string =~ s/ +/ /g;
$string =~ s/ +,/,/g;
$string =~ s/, +/,/g;
$string =~ s/,/, /g;
$string =~ s/ +/ /g;
$string =~ s/\s+/ /g;
$string =~ s/&quot;//g;
$string =~ s/’/'/g;
$string =~ s//'/g;
$string =~ s//œ/g;
$string =~ s/Premier ministrePremier ministre/Premier ministre/g;

$keywords = "";
#print $string."\n";
if ($string =~ /<meta name="LEGISLATURE_SESSION"([^>]+)>/) {
  $line = $1;
  $line =~ s/^.*content="(\d+)(ème)?[^"]*".*$/\1/;
  $legislature = $line;
}
if ($string =~ /<meta name="AUTEUR"([^>]+)>/) {
  $line = $1;
  $line =~ s/^.*content="([^"]+)".*$/\1/;
  $line =~ s/Premier Ministre Premier ministre/Premier Ministre/;
  $auteurs = $line;
}
if ($string =~ /<meta name="DATE_DEPOT"([^>]+)>/) {
  $line = $1;
  $line =~ s/^.*content="([^"]+)".*$/\1/;
  if ($line =~ /(\d{1,2})\/(\d{2})\/(\d{4})/) {
$date0 = $3.'-'.$2.'-'.sprintf('%02d', $1);
  }
}
if ($string =~ /<meta name="DATE_PUBLICATION"([^>]+)>/) {
  $line = $1;
  $line =~ s/^.*content="([^"]+)".*$/\1/;
  if ($line =~ /(\d{1,2})\/(\d{2})\/(\d{4})/) {
$date1 = $3.'-'.$2.'-'.sprintf('%02d', $1);
  }
}
if ($string =~ /<meta name="(DIVISION_DATE_DISTRIBUTION|DATE_LIGNE)"([^>]+)>/ && !$date1) {
  $line = $1;
  $line =~ s/^.*content="([^"]+)".*$/\1/;
  if ($line =~ /(\d{1,2})\/(\d{2})\/(\d{4})/) {
$date1 = $3.'-'.$2.'-'.sprintf('%02d', $1);
  }
}
if ($string =~ /<meta name="URL_DOSSIER"([^>]+)>/) {
  $line = $1;
  $line =~ s/^.*content="[^"]+\/([^"]+)\.asp(#[^"]+)?".*$/\1/;
  $dossier = $line;
}
if ($string =~ /<meta name="TYPE_DOCUMENT"([^>]+)>/) {
  $line = $1;
  $line =~ s/^.*content="([^"]+)".*$/\1/;
  $type0 = $line;
}
if ($string =~ /<meta name="INTITULE_CLASSE_ESPECE"([^>]+)>/ && !$type0) {
  $line = $1;
  $line =~ s/^.*content="([^"]+)".*$/\1/;
  $type0 = $line;
}
if ($string =~ /<meta name="LIBELLE_ESPECE"([^>]+)>/) {
  $line = $1;
  $line =~ s/^.*content="([^"]+)".*$/\1/;
  if (!($line =~ /^tel quel$/)) {
$type1 = $line;
  }
}
if ($string =~ /<meta name="TITRE"([^>]+)>/) {
  $line = $1;
  $line =~ s/^.*content="([^"]+)".*$/\1/;
  $line =~ s/\s*\(([n°\s]+)?\d+( rect)?(ifié)?\)\s*$//;
  $titre = $line;
}
if ($string =~ /<meta name="RUBRIQUE"([^>]+)>/) {
  $line = $1;
  $line =~ s/^.*content="([^"]+)".*$/\1/;
  $categorie = $line;
}
if ($string =~ /<meta name="DIVISION_INTITULE"([^>]+)>/ && !$categorie) {
  $line = $1;
  $line =~ s/^.*content="([^"]+)".*$/\1/;
  $categorie = $line;
}
if ($string =~ /<meta name="SOUS_RUBRIQUE"([^>]+)>/) {
  $line = $1;
  $line =~ s/^.*content="([^"]+)".*$/\1/;
  $keywords .= $line.".";
}
if ($string =~ /<meta name="MOTS_CLES"([^>]+)>/) {
  $line = $1;
  $line =~ s/^.*content="([^"]+)".*$/\1/;
  $keywords .= $line.".";
}

$keywords = lc $keywords;
$keywords =~ s/^\s+//;
$keywords =~ s/\s+$//;
$keywords =~ s/À/à/g;
$keywords =~ s/É/é/g;
$keywords =~ s/È/è/g;
$keywords =~ s/Ê/ê/g;
$keywords =~ s/Î/î/g;
$keywords =~ s/Ï/ï/g;
$keywords =~ s/Ô/ô/g;
$keywords =~ s/Ù/ù/g;
$keywords =~ s/Ç/ç/g;
$keywords =~ s/ +\././g;
$keywords =~ s/\. +/./g;
$keywords =~ s/\.+/./g;
$keywords =~ s/^\.+//g;
$keywords =~ s/\.+$//g;
$keywords =~ s/([\s\(,\.])l\./\1L/gi;

if ($categorie =~ /Texte de la commission/i && $id =~ /-a0/) {
  $categorie = "";
  $type0 = "Texte de la commission";
}
$categorie = lc $categorie;
$categorie =~ s/^\s+//;
$categorie =~ s/\s+$//;
$categorie =~ s/À/à/g;
$categorie =~ s/É/é/g;
$categorie =~ s/È/è/g;
$categorie =~ s/Ê/ê/g;
$categorie =~ s/Î/î/g;
$categorie =~ s/Ï/ï/g;
$categorie =~ s/Ô/ô/g;
$categorie =~ s/Ù/ù/g;
$categorie =~ s/Ç/ç/g;

$string =~ s/<[^>]*>//gi;
$string =~ s/"//gi;
$string =~ s/[\n\t]/ /gi;
$string =~ s/  +/ /gi;
$string =~ s/\\/-/gi;

$string =~ s/if \(window!= top\) top\.location\.href=location\.href//i;
$string =~ s/Recherche \| Aide \| Plan du site Accueil \&gt\; Documents parlementaires \&gt\; Les rapports législatifs//i;
$string =~ s/_____ ASSEMBL'E NATIONALE CONSTITUTION DU 4 OCTOBRE 1958 TREIZIÈME LÉGISLATURE//i;
$string =~ s/__*//i;
#print "\n";
print '{"source": "'.$source.'", "legislature": "'.$legislature.'", "id": "'.$id.'", "numero": "'.$num.'", "annexe": "'.$annexe.'", "date_depot": "'.$date0.'", "date_publi": "'.$date1.'", "auteurs": "'.$auteurs.'", "dossier": "'.$dossier.'", "type": "'.$type0.'", "type_details": "'.$type1.'", "titre": "'.$titre.'", "categorie": "'.$categorie.'", "motscles": "'.$keywords.'", "contenu": "'.$string.'"}'."\n";
