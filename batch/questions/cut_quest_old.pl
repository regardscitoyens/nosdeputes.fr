#!/usr/bin/perl

$file = shift;
$utf8_encode = shift;

use HTML::TokeParser;

$source = $file;
$source =~ s/_/\//g;
$source =~ s/(html|wget)\/?//g;

open(FILE, $file) ;
@string = <FILE>;
$string = "@string";
close FILE;
if ($utf8_encode) {
    utf8::encode($string);
}
$string =~ s/\n//g;
$string =~ s/\r//g;
$string =~ s/\<!--.+?--\>//sig;
$string =~ s/\<\/?html>//sig;
$string =~ s/\<\/?(meta|body|head|em)[^>]*>//sig;
$string =~ s/\<br\>.*\n//g;
$string =~ s/&#8217;/'/g;
$string =~ s/&#339;/oe/g;
$string =~ s/"//g;
$string =~ s/&#8211;/-/g;
$string =~ s/&nbsp;/ /g;

$legislature = $1
    if  ($string =~ /\<LEG\>(\d+).+\<\/LEG\>/);
$id = $1
    if ($string =~ /\<NUM\>(\d+)\<\/NUM\>/i);
$type = $1
    if ($string =~ /\<NAT\>\s*(.*)\s*\<\/NAT\>/i);
$mini = $1
    if ($string =~ /\<MINI\>\s*(.*)\<\/MINI\>/i);
$mina = $1 
    if ($string =~ /\<MINA\>\s*(.*)\<\/MINA\>/i);
$date = $1 
    if ($string =~ /\<DPQ\>\s*(.*)\s*\<\/DPQ\>/i);
$rubrique = $1 
    if ($string =~ /\<RUB\>\s*(.*)\s*\<\/RUB\>/i);
$tana = $1
    if ($string =~ /\<TANA\>\s*([^<]+)\s*\<\/TANA\>/i);
$ana = $1
    if ($string =~ /\<ANA\>\s*([^<]+)\s*\<\/ANA\>/i);
$question = $1 
    if ($string =~ /<QUEST>\s*(.+)\s*<\/QUEST>/i);
$question =~ s/<\/?[^>]+>//;
$question = '<p>'.$question.'</p>'
    if ($question);

#comme dans le champ <AUT> le nom n'est pas dans le bon ordre, on s'en sert pour le repérer dans la question
$pre_auteur = $1
    if ($string =~ /<AUT>\s*(Le\s\S+)/);
if (!$pre_auteur) {
    $pre_auteur = $1.$2
	if ($string =~ /<AUT>\s*(Des |des |de La |de la |de l\'|de |du )?(\S+)/);
}
$auteur = $1
    if ($question =~ /^[^M]*(M[me\.]+.+$pre_auteur\S*)\s[^M\.\,]+\s+M/);
$auteur =~ s/\s[^A-Z]+$//;
#Si on n'a pas trouvé l'auteur, on tente d'enlever les caractères spéciaux
if (!$auteur || $auteur !~ /^\S+\s+\S+/) {
    $pre_auteur =~ s/[^a-z]/ /g;
    $pre_auteur =~ s/.* ([a-z]+)$/$1/g;
    $pre_auteur =~ s/\s+//g;
    $auteur = $1
	if ($question =~ /^[^M]*(M[me\.]+.+$pre_auteur[^\.\,M]+)\s+M/);
    $auteur =~ s/\s[^A-Z]+$//;
}
#Sinon on choisi tout de même le champ AUT
if (!$auteur  || $auteur !~ /^\S+\s+\S+/ || length($auteur) > 30) {
    $auteur = $1
	if ($string =~ /<AUT>\s*([^<]+\S)\s*</);
}
$reponse = $1 
    if ($string =~ /\<REP\>\s*(.+\S)\s*\<\/REP\>/i);
$reponse = '<p>'.$reponse.'</p>'
    if ($reponse) ;

if ($source =~ /^13/) {
    $source = 'q13/'.$source;
}

$date =~ s/^(\d+)\/(\d+)\/(\d+)$/\3-\2-\1/g;
print '{"source": "'.$source.'", "legislature": "'.$legislature.'", "numero": "'.$id.'", "date": "'.$date.'", "auteur": "'.$auteur.'", "ministere_interroge": "'.$mini.'", "ministere_attributaire": "'.$mina.'", "rubrique":"'.$rubrique.'", "tete_analyse": "'.$tana.'", "analyse": "'.$ana.'", "question": "'. $question .'", "reponse": "'.$reponse.'", "type": "'.$type.'" } '."\n";
