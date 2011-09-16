#!/usr/bin/perl

use WWW::Mechanize;
use HTML::TokeParser;
use  URI::Escape;
use Encode;


$a = WWW::Mechanize->new();
$start = shift || '0';
$count = 50;
$ok = 1;

open FILE, "conf/cr.list";
@indexes = <FILE>;
close FILE;

foreach $index (@indexes) {
    chomp($index);
    eval {$a->get($index);};
    if ($a->status() == 404) {
	print "ERR: bad file in index : 404 on $index\n";
	next;
    }
    $content = $a->content;
    $p = HTML::TokeParser->new(\$content);
    while ($t = $p->get_tag('a')) {
	if ($t->[1]{href} =~ /compte-rendu-commissions/ || $t->[1]{href} =~ /\d{6}\d*.html/) {
	    $curl =  $t->[1]{href};
	    next if ($curl =~ /\/presse\// || curl =~ /prog.html/);
	    next if ($url{$curl});
	    $uriurl = $curl;
	    $uriurl =~ s/^\//http:\/\/www.senat.fr\//;
 	    next if -e "html/".uri_escape($uriurl);
	    eval {$a->get($curl);};
	    if ($a->status() == 404) {
		print "ERR: 404 ".$a->uri()." (from $index)\n ";
		$a->back();
		next;
	    }
	    $uri = $a->uri();
	    if ($url{$uri}) { $a->back(); next; }
            $url{$curl} = 1;	    
            $url{$uri} = 1;	    
	    $file =  uri_escape($uri);
 	    if (-e "html/$file") { $a->back(); next; }
	    print "$file\n";
	    open FILE, ">:utf8", "html/$file.tmp";
	    print FILE $a->content;
	    close FILE;
	    rename "html/$file.tmp", "html/$file"; 
	    $a->back();
	}
    }
}

