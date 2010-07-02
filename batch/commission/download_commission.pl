#!/usr/bin/perl

use WWW::Mechanize;
use HTML::TokeParser;

$a = WWW::Mechanize->new();
$start = shift || '0';
$count = 50;
$ok = 1;
while ($ok) {
    $ok = 0;
    last if ($start > 300);
    $a->get('http://recherche2.assemblee-nationale.fr/resultats_generique.jsp?texterecherche=*&typedoc=crreunions&auteurid=&legislatureNum=13&categoryid=&ResultCount='.$count.'&ResultStart='.$start);
    $content = $a->content;
    $p = HTML::TokeParser->new(\$content);
    while ($t = $p->get_tag('a')) {
	$txt = $p->get_text('/a');
	if ($txt =~ /compte rendu|e nationale \~/i) {
	    $ok = 1;
	    $curl = $file = $t->[1]{href};
	    $file =~ s/\//_/gi;
	    $file =~ s/\#.*//;
	    $curl =~ s/[^\/]+$//;
            $url{$curl} = 1;
 	    next if -e "html/$file";
	    print "$file\n";
	    $a->get($t->[1]{href});
	    open FILE, ">:utf8", "html/$file.tmp";
	    print FILE $a->content;
	    close FILE;
	    rename "html/$file.tmp", "html/$file"; 
	    $a->back();
	}
    }
    $start += $count;
}

@url = keys %url;
push(@url, "http://www.assemblee-nationale.fr/13/budget/plf2010/commissions_elargies/cr/", "http://www.assemblee-nationale.fr/13/cr-mec/07-08/index.asp", "http://www.assemblee-nationale.fr/13/cr-mec/08-09/index.asp", "http://www.assemblee-nationale.fr/13/cr-mec/09-10/index.asp");
$a = WWW::Mechanize->new();

foreach $url (@url) {

    $a->get($url);
    $content = $a->content;
    $p = HTML::TokeParser->new(\$content);

    $cpt = 0;
    
    while ($t = $p->get_tag('a')) {
	$txt = $p->get_text('/a');
	if ($txt =~ /compte rendu|mission/i && $t->[1]{href} =~ /\d\.asp/) {
	    $a->get($t->[1]{href});
	    $file = $a->uri();
	    $file =~ s/\//_/gi;
	    $file =~ s/\#.*//;
	    $size = -s "html/$file";
            if ($size) {
                $cpt++;
                last if ($cpt > 3);
                next;
            }
	    print "$file\n";
	    open FILE, ">:utf8", "html/$file";
	    print FILE $a->content;
	    close FILE;
	    $a->back();
	}
    }
}
