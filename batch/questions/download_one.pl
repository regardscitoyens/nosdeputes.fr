#!/usr/bin/perl

use utf8;
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
$content = $a->content;
if ($content =~ s/iso-8859-1/utf-8/gi) {
  $content = decode("windows-1252", $content);
}
print FILE $content;
close FILE;
print "$file downloaded.\n" if ($verbose);

