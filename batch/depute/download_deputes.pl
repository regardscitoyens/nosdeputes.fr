#!/usr/bin/perl

use WWW::Mechanize;
use HTML::TokeParser;

$a = WWW::Mechanize->new();
$a->get("http://www.assemblee-nationale.fr/13/tribun/xml/liste_alpha.asp");

$content = $a->content;
$p = HTML::TokeParser->new(\$content);

while ($t = $p->get_tag('a')) {
    if ($t->[1]{class} eq 'dep2') {
	$uri = $file = $t->[1]{href};
	$file =~ s/^.*\/([^\/]+)/$1/;
	print "$file\n";
	$a->get($uri);
	open FILE, ">:utf8", "html/$file";
	print FILE $a->content;
	close FILE;
    }
}
