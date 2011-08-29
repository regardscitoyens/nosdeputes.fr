#!/usr/bin/perl

use WWW::Mechanize;
use URI::Escape;
use Encode;

$url = shift;
$verbose = shift || 0;
$url =~ s/^\s+//;
$a = WWW::Mechanize->new();
$a->get($url);
$file = uri_escape($a->uri);
print " saving $url ... " if ($verbose);
open FILE, ">:utf8", "html/$file";
$content = decode("windows-1252", $a->content);
$content =~ s/iso-8859-1/utf-8/g;
print FILE $content;
close FILE;
print "$file downloaded.\n" if ($verbose);

