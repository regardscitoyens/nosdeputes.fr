#!/usr/bin/perl

print "ce script est obsolète il permet de télécharger et parser toutes les questions de la 13ème legislature\n";
exit;

use WWW::Mechanize;
use HTML::TokeParser;
$dont_parse = shift || 0;
$total_quest = 0;

@urls = (
#"http://recherche2.assemblee-nationale.fr/questions/resultats-questions.jsp?NumLegislature=13Questions&Dates=DPQ&Scope=TEXTEINTEGRAL&SortField=DATE&SortOrder=Asc&format=HTML",
    "http://recherche2.assemblee-nationale.fr/resultats_questions.jsp?texterecherche=*&auteurid=&legislatureNum=13&categoryid="
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
	
	
	unless ($dont_download) {
	    $nb = 0;
	    $a = WWW::Mechanize->new();
	    $a->get($url);
	    $content = $a->content;
	    $p = HTML::TokeParser->new(\$content);
	    
	    while ($t = $p->get_tag('a')) {
		if ($t->[1]{class} eq 'sousmenu') {
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
		    $nb++;
		    unless ($dont_parse) {
			`perl cut_quest.pl html/$htmfile >> $file`;
		    }
		    print "done.\n";
		$a->back();
		}
	    }
	    close FILE;
	    $count ++;
	    last unless ($nb);
	}
    }
}    
print $total_quest." questions parsées\n";

