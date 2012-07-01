#!/usr/bin/perl

use Date::Format;
use WWW::Mechanize;
use HTML::TokeParser;
$count = 0;
$legislature = shift || 14;
$loi = shift;

$url = "http://recherche2.assemblee-nationale.fr/amendements/resultats.jsp?typeEcran=avance&chercherDateParNumero=non&NUM_INIT=".$loi."&NUM_AMEND=&AUTEUR=&DESIGNATION_ARTICLE=&DESIGNATION_ALINEA=&SORT_EN_SEANCE=&DELIBERATION=&NUM_PARTIE=&DateDebut=&DateFin=&periode=&LEGISLATURE=".$legislature."Amendements&QueryText=&Scope=TEXTEINTEGRAL&SortField=ORDRE_TEXTE&SortOrder=Asc&searchadvanced=Rechercher&ResultMaxDocs=5000&ResultCount=5000";

$a = WWW::Mechanize->new();
$a->get($url);
$content = $a->content;
$p = HTML::TokeParser->new(\$content);

while ($t = $p->get_tag('a')) {
    if ($t->[1]{class} eq 'lienamendement') {
	$htmfile = $t->[1]{href};
	next if ($htmfile =~ /(index|javascript)/);
        $count++;
	$a->get($htmfile);
	$htmfile =~ s/^\s+//gi;
	$htmfile =~ s/\//_/gi;
	$htmfile =~ s/\#.*//;
	print "  $htmfile ... ";
	open FILE, ">:utf8", "html/$htmfile";
	print FILE $a->content;
	close FILE;
	print "downloaded.\n";
	$a->back();
    }
}
print $count." amendements pour les deux dernières semaines\n";


