#!/usr/bin/perl

print "ce script est obsolète il permet de télécharger et parser tous les amendements de 2007 à 2009\n";
exit;

use WWW::Mechanize;
use HTML::TokeParser;
$total_amdmts = 0;

@urls = ("http://recherche2.assemblee-nationale.fr/amendements/resultats.jsp?ResultMaxDocs=100&LEGISLATURE=13Amendements&DateDebut=15%2F04%2F2007&DateFin=15%2F08%2F2007&Scope=TEXTEINTEGRAL&SortField=DATE&SortOrder=Asc&format=HTML",
	 "http://recherche2.assemblee-nationale.fr/amendements/resultats.jsp?ResultMaxDocs=100&LEGISLATURE=13Amendements&DateDebut=15%2F08%2F2007&DateFin=15%2F12%2F2007&Scope=TEXTEINTEGRAL&SortField=DATE&SortOrder=Asc&format=HTML",
	 "http://recherche2.assemblee-nationale.fr/amendements/resultats.jsp?ResultMaxDocs=100&LEGISLATURE=13Amendements&DateDebut=15%2F12%2F2007&DateFin=15%2F04%2F2008&Scope=TEXTEINTEGRAL&SortField=DATE&SortOrder=Asc&format=HTML",
	 "http://recherche2.assemblee-nationale.fr/amendements/resultats.jsp?ResultMaxDocs=100&LEGISLATURE=13Amendements&DateDebut=15%2F04%2F2008&DateFin=15%2F08%2F2008&Scope=TEXTEINTEGRAL&SortField=DATE&SortOrder=Asc&format=HTML",
	 "http://recherche2.assemblee-nationale.fr/amendements/resultats.jsp?ResultMaxDocs=100&LEGISLATURE=13Amendements&DateDebut=15%2F08%2F2008&DateFin=15%2F12%2F2008&Scope=TEXTEINTEGRAL&SortField=DATE&SortOrder=Asc&format=HTML",
	 "http://recherche2.assemblee-nationale.fr/amendements/resultats.jsp?ResultMaxDocs=100&LEGISLATURE=13Amendements&DateDebut=15%2F12%2F2008&DateFin=15%2F04%2F2009&Scope=TEXTEINTEGRAL&SortField=DATE&SortOrder=Asc&format=HTML",
	 "http://recherche2.assemblee-nationale.fr/amendements/resultats.jsp?ResultMaxDocs=100&LEGISLATURE=13Amendements&DateDebut=15%2F04%2F2009&DateFin=15%2F08%2F2009&Scope=TEXTEINTEGRAL&SortField=DATE&SortOrder=Asc&format=HTML"
);

$trimestre = 0;
foreach $url0 (@urls) {
$trimestre++;

$a = WWW::Mechanize->new();
$a->get($url0."&ResultCount=50&ResultStart=1");
$content = $a->content;
$p = HTML::TokeParser->new(\$content);
while ($t = $p->get_tag('span')) {
    if ($t->[1]{style} eq 'color:#C2262A; font-weight: bold;') {
	$n_amdmts = $p->get_text('/span');
    }
}
$n_pages = $n_amdmts / 50;
print $n_amdmts."\n";
$total_amdmts = $total_amdmts + $n_amdmts;

for ($i = 0; $i <= $n_pages; $i++) {

$start = $i*50+1;
$url = $url0."&ResultCount=50&ResultStart=".$start;
$file = "xml/amendements_13_trimestre_".$trimestre."_".$i.".xml";
print $url." > ".$file."\n";
	
$a = WWW::Mechanize->new();
$a->get($url);
$content = $a->content;
$p = HTML::TokeParser->new(\$content);

while ($t = $p->get_tag('a')) {
    if ($t->[1]{class} eq 'lienamendement') {
#	$a->get($t->[1]{href});
#	$htmfile = $a->uri();
	$htmfile = $t->[1]{href};
	next if ($htmfile =~ /(index|javascript)/);
	$htmfile =~ s/\//_/gi;
	$htmfile =~ s/\#.*//;
	print "  $htmfile ... ";	
#	open FILE2, ">:utf8", "html/$htmfile";
#	print FILE2 $a->content;
#	close FILE2;
#	print "downloaded ... ";
	`perl cut_amdmt.pl html/$htmfile >> $file`;
	print "done.\n";
	$a->back();
    }
}
close FILE;
$count ++;
}
}
print $total_amdmts." amendements parsés\n";

