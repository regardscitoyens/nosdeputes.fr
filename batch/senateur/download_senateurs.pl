#!/usr/bin/perl
use WWW::Mechanize;
use HTML::TokeParser;
use URI::Escape;
use Encode;

$|=1;

$verbose = shift || 0;
%done = ();
sub download_fiche {
	$uri = shift;
	return if ($done{$uri});
	$done{$uri} = 1;
	$a->get($uri);
	$file = uri_escape($a->uri());
	return if ($done{$file});
	$done{$file} = 1;
        print "saving " if ($verbose);
	print $file;
	print " : " if ($verbose);
	mkdir html unless -e "html/" ;
	open FILE, ">:utf8", "html/$file";
	$thecontent = $a->content;
	$thecontent =~ s/iso-8859-1/utf8/g;
	print FILE $thecontent;
	close FILE;
        print "DONE" if ($verbose);
	print "\n";
	return $file;
}
$a = WWW::Mechanize->new();

sub find_senateurs {
	$url = shift;
	print "looking for senateurs in $url\n" if ($verbose);
	$a->get($url);
	$content = $a->content;
	$p = HTML::TokeParser->new(\$content);
	while ($t = $p->get_tag('a')) {
	    if ($t->[1]{href} =~ /\/(senateur|senfic)\//) {
		download_fiche($t->[1]{href});
	    }
	}
}

find_senateurs("http://www.senat.fr/senateurs/senatl.html");
find_senateurs("http://www.senat.fr/senateurs/news.html");
