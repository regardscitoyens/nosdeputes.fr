#!/usr/bin/perl

use Date::Format;
use WWW::Mechanize;
use HTML::TokeParser;
$link_loi = shift;

if ($link_loi =~ /^http\:\/\/.*[a-z](\d+)\.asp$/) {
  print 'Download loi N°'.$1.' ...';
} else {
  print 'ERROR link';
  exit;
}

$a = WWW::Mechanize->new();
$a->get($link_loi);
$htmfile = $link_loi; 
$htmfile =~ s/^\s+//gi;
$htmfile =~ s/\//_/gi;
$htmfile =~ s/:/-/gi;
$htmfile =~ s/\#.*//;
print 'in html/'.$htmfile;
open FILE, ">:utf8", "html/$htmfile";
print FILE $a->content;
close FILE;
 

