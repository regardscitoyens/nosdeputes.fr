#!/usr/bin/perl

use Date::Format;
use WWW::Mechanize;
use HTML::TokeParser;
$count = 0;
$a = WWW::Mechanize->new();
$htmfile = shift;
    $htmfile =~ s/^\s+//gi;
    $count++;
    $a->get($htmfile);
    $htmfile =~ s/\//_/gi;
    $htmfile =~ s/\#.*//;
    print "  $htmfile ... ";
    open FILE, ">:utf8", "html/$htmfile";
    print FILE $a->content;
    close FILE;
    print "downloaded.\n";
    $a->back();

