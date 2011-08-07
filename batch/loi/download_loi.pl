#!/usr/bin/perl

use Date::Format;
use WWW::Mechanize;
use HTML::TokeParser;
$link_loi = shift;

if ($link_loi =~ /^http\:\/\/.*(senat).*[a-z](\d\d-\d{1,4})\.html$/ || $link_loi =~ /^http\:\/\/.*(assemblee).*[a-z](\d+)-a0\.asp$/ || $link_loi =~ /^http\:\/\/.*(assemblee).*[a-z](\d+)\.asp$/ ) {
  print 'Download loi '.$1.' N°'.$2.' ...';
} else {
  print "ERROR link\n";
  exit;
}

$a = WWW::Mechanize->new();
$a->get($link_loi);
$htmfile = $link_loi; 
$htmfile =~ s/^\s+//gi;
$htmfile =~ s/\//_/gi;
$htmfile =~ s/:/-/gi;
$htmfile =~ s/\#.*//;
print 'in html/'.$htmfile."\n";
open FILE, ">:utf8", "html/$htmfile";
print FILE $a->content;
close FILE;
 

