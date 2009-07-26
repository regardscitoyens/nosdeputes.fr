#!/usr/bin/perl

use WWW::Mechanize;
use HTML::TokeParser;

$a = WWW::Mechanize->new();
$a->get("http://recherche2.assemblee-nationale.fr/amendements/resultats.jsp?LEGISLATURE=13Amendements&Scope=TEXTEINTEGRAL&SortField=DATE&SortOrder=Asc&format=HTML&ResultCount=50000&searchadvanced=Rechercher");

$content = $a->content;
$p = HTML::TokeParser->new(\$content);

open FILE, ">:utf8", "txt/amendements_13.txt";
while ($t = $p->get_tag('a')) {
    if ($t->[1]{class} eq 'lienamendement') {
	$a->get($t->[1]{href});
	$file = $a->uri();
	next if ($file =~ /(index|javascript)/);
	$file =~ s/\//_/gi;
	$file =~ s/\#.*//;
	print "$file ... ";	
	open FILE2, ">:utf8", "html/$file";
	print FILE2 $a->content;
	close FILE2;
	print "downloaded ... ";
	print FILE `perl cut_amdmt.pl html/$file`;
	print "done.\n";		
	$a->back();
    }
}
close FILE;

