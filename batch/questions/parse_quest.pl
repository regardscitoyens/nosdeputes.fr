#!/usr/bin/perl

use WWW::Mechanize;
use HTML::TokeParser;
$total_quest = 0;

@urls = ("http://recherche2.assemblee-nationale.fr/questions/resultats-questions.jsp?NumLegislature=13Questions&Dates=DPQ&DATINF=15%2F04%2F2007&DATSUP=15%2F08%2F2007&Scope=TEXTEINTEGRAL&SortField=DATE&SortOrder=Asc&format=HTML",
	 "http://recherche2.assemblee-nationale.fr/questions/resultats-questions.jsp?NumLegislature=13Questions&Dates=DPQ&DATINF=15%2F08%2F2007&DATSUP=15%2F12%2F2007&Scope=TEXTEINTEGRAL&SortField=DATE&SortOrder=Asc&format=HTML",
	 "http://recherche2.assemblee-nationale.fr/questions/resultats-questions.jsp?NumLegislature=13Questions&Dates=DPQ&DATINF=15%2F12%2F2007&DATSUP=15%2F04%2F2008&Scope=TEXTEINTEGRAL&SortField=DATE&SortOrder=Asc&format=HTML",
	 "http://recherche2.assemblee-nationale.fr/questions/resultats-questions.jsp?NumLegislature=13Questions&Dates=DPQ&DATINF=15%2F04%2F2008&DATSUP=15%2F08%2F2008&Scope=TEXTEINTEGRAL&SortField=DATE&SortOrder=Asc&format=HTML",
	 "http://recherche2.assemblee-nationale.fr/questions/resultats-questions.jsp?NumLegislature=13Questions&Dates=DPQ&DATINF=15%2F08%2F2008&DATSUP=15%2F12%2F2008&Scope=TEXTEINTEGRAL&SortField=DATE&SortOrder=Asc&format=HTML",
	 "http://recherche2.assemblee-nationale.fr/questions/resultats-questions.jsp?NumLegislature=13Questions&Dates=DPQ&DATINF=15%2F12%2F2008&DATSUP=15%2F04%2F2009&Scope=TEXTEINTEGRAL&SortField=DATE&SortOrder=Asc&format=HTML",
	 "http://recherche2.assemblee-nationale.fr/questions/resultats-questions.jsp?NumLegislature=13Questions&Dates=DPQ&DATINF=15%2F04%2F2009&DATSUP=15%2F08%2F2009&Scope=TEXTEINTEGRAL&SortField=DATE&SortOrder=Asc&format=HTML"
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
	$n_quest = $p->get_text('/span');
    }
}
$n_pages = $n_quest / 50;
print $n_quest."\n";
$total_quest = $total_quest + $n_quest;

for ($i = 0; $i <= $n_pages; $i++) {

$start = $i*50+1;
$url = $url0."&ResultCount=50&ResultStart=".$start;
$file = "input/questions_13_trimestre_".$trimestre."_".$i.".xml";
print $url." > ".$file."\n";
	
$a = WWW::Mechanize->new();
$a->get($url);
$content = $a->content;
$p = HTML::TokeParser->new(\$content);

while ($t = $p->get_tag('a')) {
    if ($t->[1]{class} eq 'lienamendement') {
	$a->get($t->[1]{href});
	$htmfile = $a->uri();
#	$htmfile = $t->[1]{href};
	next if ($htmfile =~ /(index|javascript)/);
	$htmfile =~ s/\//_/gi;
	$htmfile =~ s/\#.*//;
	$htmfile =~ s/.*\.fr_?//;
	print "  $htmfile ... ";	
	open FILE2, ">:utf8", "html/$htmfile";
	print FILE2 $a->content;
	close FILE2;
	print "downloaded ... ";
	`perl cut_quest.pl html/$htmfile >> $file`;
	print "done.\n";
	$a->back();
    }
}
close FILE;
$count ++;
}
}
print $total_quest." amendements parsés\n";

