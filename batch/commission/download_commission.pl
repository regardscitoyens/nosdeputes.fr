#!/usr/bin/perl

use WWW::Mechanize;
use HTML::TokeParser;
use  URI::Escape;
use Encode;

@indexes = ("http://www.senat.fr/compte-rendu-commissions/affaires-etrangeres.html", "http://www.senat.fr/compte-rendu-commissions/affaires-etrangeres_archives.html", "http://www.senat.fr/compte-rendu-commissions/controle-de-la-securite-sociale.html", "http://www.senat.fr/compte-rendu-commissions/controle-de-la-securite-sociale_archives.html", "http://www.senat.fr/compte-rendu-commissions/affaires-sociales.html", "http://www.senat.fr/compte-rendu-commissions/affaires-sociales_archives.html", "http://www.senat.fr/compte-rendu-commissions/culture.html", "http://www.senat.fr/compte-rendu-commissions/culture_archives.html", "http://www.senat.fr/compte-rendu-commissions/economie.html", "http://www.senat.fr/compte-rendu-commissions/economie_archives.html", "http://www.senat.fr/compte-rendu-commissions/finances.html", "http://www.senat.fr/compte-rendu-commissions/finances_archives.html", "http://www.senat.fr/compte-rendu-commissions/lois.html", "http://www.senat.fr/compte-rendu-commissions/lois_archives.html");

$a = WWW::Mechanize->new();
$start = shift || '0';
$count = 50;
$ok = 1;
foreach $index (@indexes) {
    $a->get($index);
    $content = $a->content;
    $p = HTML::TokeParser->new(\$content);
    while ($t = $p->get_tag('a')) {
	if ($t->[1]{href} =~ /compte-rendu-commissions/) {
	    $curl =  $t->[1]{href};
	    next if ($url{$curl});
            $url{$curl} = 1;
	    $a->get($t->[1]{href});
	    $file =  uri_escape($a->uri());
 	    next if -e "html/$file";
	    print "$file\n";
	    open FILE, ">:utf8", "html/$file.tmp";
	    print FILE $a->content;
	    close FILE;
	    rename "html/$file.tmp", "html/$file"; 
	    $a->back();
	}
    }
}

