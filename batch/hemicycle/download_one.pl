#!/usr/bin/perl

use WWW::Mechanize;
use HTML::TokeParser;
use URI::Escape;
use Encode;

$href = shift;

$a = WWW::Mechanize->new();
eval {$a->get($href);};
if ($@) {
    print STDERR "error downloading $href\n";
    $a->back();
    exit 1;
}
$file = uri_escape($a->uri());
open FILE, ">:utf8", "html/$file";
$thecontent = $a->content;
if ($thecontent =~ s/iso-8859-1/utf-8/gi) {
    $thecontent = decode("windows-1252", $thecontent);
}
print FILE $thecontent;
close FILE;
print "$file\n";

