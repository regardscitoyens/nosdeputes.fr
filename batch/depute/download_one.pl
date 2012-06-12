#!/bin/perl

use WWW::Mechanize;
$a = WWW::Mechanize->new();

$uri = $file = shift;
$file =~ s/^.*\/([^\/]+)/$1/;
print "$file\n" if ($verbose);
$a->get($uri);
mkdir html unless -e "html/" ;
open FILE, ">", "html/$file";
print FILE $a->content;
close FILE;


