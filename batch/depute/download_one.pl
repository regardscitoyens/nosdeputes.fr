#!/bin/perl

use WWW::Mechanize;
$a = WWW::Mechanize->new();

$uri = $file = shift;
$file =~ s/^.*\/([^\/]+)/$1/;
print "$file : $uri\n";
$a->get($uri);
mkdir html unless -e "html/";
open FILE, ">:utf-8", "html/$file" || warn("cannot write on html/$file");
print FILE $a->content;
close FILE;

