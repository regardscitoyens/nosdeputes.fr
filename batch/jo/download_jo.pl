#!/usr/bin/perl

use WWW::Mechanize;
$date = shift;
$pdf_file = shift || "/tmp/jo.pdf"; 

#On récupère la référence du JO du jour

my $agent = WWW::Mechanize->new();
$agent->get("http://www.journal-officiel.gouv.fr/users.php?date_jo=$date");
if ($agent->{content} !~ /(publi.*pdf.sig)/) {
    exit 1;
}

#Telecharge le sommaire html

$uri = "/$1";
$url = 'http://www.journal-officiel.gouv.fr'.$uri;
$url_html = $url;
$url_html =~ s/p000.pdf.sig/sx00.html/;
$agent->get($url_html);


#Recherche le chapitre destiné aux commissions

$doc = $agent->{content};
$doc =~ s/\n/ /g;
if ($doc !~ /class="rubrique_02">Assembl&eacute;e nationale<\/p>(.*)COMMISSIONS/ || ($doc =~ /S&eacute;nat/ && $doc !~ /COMMISSIONS.*S&eacute;nat/)) {
    exit 1;
}

$doc =~ s/^.*class="rubrique_02">Assembl&eacute;e nationale<\/p>//;
$doc =~ s/class="rubrique_02">S&eacute;nat.*$//;
if ($doc =~ /(.*)COMMISSIONS/) {
    $doc = $1;
}


#Puis l'url vers le pdf

if ($doc !~ /(joe_[\d_]+.pdf.sig)\W+$/) {
    exit 1;
}
$pdf = $1;
$url =~ s/joe.*/$pdf/;

#Extrait le pdf encodé en base 64 et on le sauve

$agent->get($url);
@lignes = split /\n/, $agent->{content};
$on = 0;

open PDF, "|openssl enc -d -base64 -in /dev/stdin -out $pdf_file";
foreach(@lignes) {
    if ($on) {
	$l = $_ ;
	$l =~ s/<\/xjo:Donnee.*//;
	print PDF "$l\n";
    }
    if (/xjo:Donnee/) {
	$on = !$on;
	if ($on && s/.*\<xjo:Donnee\>//) {
	    print PDF ;
	    print PDF "\n";
	}
    }
}
close PDF;
