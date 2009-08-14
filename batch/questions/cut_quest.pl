#!/usr/bin/perl

$file = shift;
use HTML::TokeParser;

$source = $file;
$source =~ s/_/\//g;
$source =~ s/html\/?//g;
	
open(FILE, $file) ;
@string = <FILE>;
$string = "@string";
close FILE;
#utf8::encode($string);
$string =~ s/\<br\>.*\n//g;
$string =~ s/&#8217;/'/g;
$string =~ s/&#339;/œ/g;
$string =~ s/&#8211;/-/g;

($legislature) = ($string =~ m/\<LEG\>(\d+).+\<\/LEG\>/is);
($id) = ($string =~ m/\<NUM\>(\d+)\<\/NUM\>/is);
($auteur) = ($string =~ m/\<AUT\>(.+?)\<\/AUT\>/is);
($type) = ($string =~ m/\<NAT\>(.+?)\<\/NAT\>/is);
($mini) = ($string =~ m/\<MINI\> ?(.+?)\<\/MINI\>/is);
($mina) = ($string =~ m/\<MINA\> ?(.+?)\<\/MINA\>/is);
($date) = ($string =~ m/\<DPQ\>(.+?)\<\/DPQ\>/is);
($rubrique) = ($string =~ m/\<RUB\>(.+?)\<\/RUB\>/is);
($tana) = ($string =~ m/\<TANA\>(.+?)\<\/TANA\>/is);
($ana) = ($string =~ m/\<ANA\>(.+?)\<\/ANA\>/is);
($question) = ($string =~ m/\<QUEST\> ?\<html\>(.+)\<\/html\> ?\<\/QUEST\>/is);
$question =~ s/\<!--.+?--\>//sig;
($reponse) = ($string =~ m/\<REP\> ?\<html\>(.+)\<\/REP\> ?\<\/html\>/is);
$date =~ s/^(\d)\/(\d)\/(\d)$/\3-\2-\1/g;
print '{"source": "'.$source.'", "legislature": "'.$legislature.'", "numero": "'.$id.'", "date": "'.$date.'", "auteur": "'.$auteur.'", "ministere_interroge": "'.$mini.'", "ministere_attributaire": "'.$mina.'", "rubrique":"'.$rubrique.'", "tete_analyse": "'.$tana.'", "analyse": "'.$ana.'", "question": "'. $question .'", "reponse": "'.$reponse.'", "type": "'.$type.'" } '."\n";
