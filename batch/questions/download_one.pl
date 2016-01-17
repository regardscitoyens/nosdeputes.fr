#!/usr/bin/perl

use Date::Format;
use WWW::Mechanize;
use HTML::TokeParser;
$a = WWW::Mechanize->new();
$htmfile = shift;
$htmfile =~ s/^\s+//gi;
$htmfile .= "/vue/xml";
$a->get($htmfile);
$htmfile =~ s/\//_/gi;
$htmfile =~ s/\#.*//;
print "  $htmfile ... ";
open FILE, ">:utf8", "html/$htmfile";
$content = $a->content;
utf8::decode($content);
print FILE $content;
close FILE;
print "downloaded.\n";
