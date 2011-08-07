#!/usr/bin/perl

use WWW::Mechanize;
use HTML::TokeParser;
$source = shift;

$a = WWW::Mechanize->new();

$a->get($source);
$file = $source;
$file =~ s/\//_/gi;
$file =~ s/\#.*//;
print "$file\n";
open FILE, ">:utf8", "html/$file";
print FILE $a->content;
close FILE;
$a->back();

