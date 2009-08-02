#!/usr/bin/perl

use WWW::Mechanize;
use HTML::TokeParser;

$legislature = shift;

$url0 = "http://recherche2.assemblee-nationale.fr/amendements/resultats.jsp?LEGISLATURE=".sprintf("%2d", $legislature)."Amendements&Scope=TEXTEINTEGRAL&SortField=DATE&SortOrder=Asc&format=HTML&ResultCount=500&searchadvanced=Rechercher";
$a = WWW::Mechanize->new();
$a->get($url0);
$content = $a->content;
$p = HTML::TokeParser->new(\$content);
while ($t = $p->get_tag('span')) {
    if ($t->[1]{style} eq 'color:#C2262A; font-weight: bold;') {
	$n_amdmts = $p->get_text('/span');
    }
}
$n_pages = $n_amdmts / 500;
print $n_amdmts."\n";

for ($i = 0; $i <= $n_pages; $i++) {

$start = $i*500+1;
$url = "http://recherche2.assemblee-nationale.fr/amendements/resultats.jsp?LEGISLATURE=".sprintf("%2d", $legislature)."Amendements&Scope=TEXTEINTEGRAL&SortField=DATE&SortOrder=Asc&format=HTML&ResultCount=500&ResultStart=".$start;
$file = "txt/amendements_".$legislature."_".$i.".txt";
print $url." > ".$file."\n";

$a = WWW::Mechanize->new();
$a->get($url);
$content = $a->content;
$p = HTML::TokeParser->new(\$content);

open FILE, ">:utf8", $file;
while ($t = $p->get_tag('a')) {
    if ($t->[1]{class} eq 'lienamendement') {
	$a->get($t->[1]{href});
	$htmfile = $a->uri();
	next if ($htmfile =~ /(index|javascript)/);
	$htmfile =~ s/\//_/gi;
	$htmfile =~ s/\#.*//;
	print "  $htmfile ... ";	
	open FILE2, ">:utf8", "html/$htmfile";
	print FILE2 $a->content;
	close FILE2;
	print "downloaded ... ";
	print FILE `perl cut_amdmt.pl html/$htmfile`;
	print "done.\n";
	$a->back();
    }
}
close FILE;
$count ++;
}
