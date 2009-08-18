#!/usr/bin/perl

use WWW::Mechanize;
use HTML::TokeParser;

@url = (
    "http://www.assemblee-nationale.fr/13/cri/2008-2009/",
);

$a = WWW::Mechanize->new();

foreach $url (@url) {
    print "url: $url\n";
    $a->get($url);
    $content = $a->content;
    $p = HTML::TokeParser->new(\$content);
    
    while ($t = $p->get_tag('a')) {
	$txt = $p->get_text('/a');
	if ($txt =~ /\d+[\Serm]+\s+\S+ance/i) {
	    $a->get($t->[1]{href});
	    $file = $a->uri();
	    $file =~ s/\//_/gi;
	    $file =~ s/\#.*//;
	    print "seance: $file\n";
	    open FILE, ">:utf8", "html/$file";
	    print FILE $a->content;
	    close FILE;
	    $a->back();
	}
    }
}
