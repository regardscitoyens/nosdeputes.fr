#!/usr/bin/perl

use WWW::Mechanize;
use HTML::TokeParser;

$a = WWW::Mechanize->new();
$start = shift || '0';
$count = 50;
$ok = 1;
while ($ok) {
    $ok = 0;
    $a->get('http://recherche2.assemblee-nationale.fr/resultats_generique.jsp?texterecherche=*&typedoc=crdebats&auteurid=&legislatureNum=13&categoryid=&ResultCount='.$count.'&ResultStart='.$start);
    $content = $a->content;
    $p = HTML::TokeParser->new(\$content);
    
    while ($t = $p->get_tag('a')) {
	$txt = $p->get_text('/a');
	if ($txt =~ /compte rendu|ance (unique )?du /i) {
	    $ok = 1;
	    $file = $t->[1]{href};
	    $file =~ s/\//_/gi;
	    $file =~ s/\#.*//;
	    print "$file\n";
	    exit if -e "html/$file";
	    $a->get($t->[1]{href});
	    open FILE, ">:utf8", "html/$file.tmp";
	    print FILE $a->content;
	    close FILE;
	    rename "html/$file.tmp", "html/$file"; 
	    $a->back();
	}
    }
    $start += $count;
    print "$start\n";
}
exit;
