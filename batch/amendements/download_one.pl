#!/usr/bin/perl

mkdir "html" unless -e "html";

use URI::Escape;
use WWW::Mechanize;
use HTML::TokeParser;
$a = WWW::Mechanize->new();
$url = shift;
eval {$a->get($url);};
if ($a->status() == 404) {
  $a->back();
  print STDERR "ERREUR 404 sur $url\n";
  return;
}
$htmfile = uri_escape($url);
print "  $htmfile ... ";
open FILE, ">:utf8", "html/$htmfile";
$thecontent = $a->content;
if ($thecontent =~ s/iso-8859-1/utf-8/gi) {
  $thecontent = decode("windows-1252", $thecontent);
}
print FILE $thecontent;
close FILE;
$a->back();
print "downloaded.\n";

