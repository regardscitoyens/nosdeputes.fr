#!/usr/bin/perl

use Date::Format;
use WWW::Mechanize;
use HTML::TokeParser;
$a = WWW::Mechanize->new();

    $url = $htmfile = shift;
    $htmfile =~ s/^\s+//gi;
    $a->get($url);
    $htmfile =~ s/\//_/gi;
    $htmfile =~ s/\#.*//;
    print "  $htmfile ... ";
    open FILE, ">:utf8", "html/$htmfile";
    print FILE $a->content;
    close FILE;
    print "downloaded.\n";
    $a->back();

