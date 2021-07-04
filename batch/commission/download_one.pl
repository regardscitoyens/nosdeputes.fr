#!/usr/bin/perl

use WWW::Mechanize;

$url = shift;
$url =~ s/^\s+//gi;
$oldstyle = shift;

$a = WWW::Mechanize->new();
$a->get($url);
$content = $a->content;

if (!$oldstyle && $content !~ /href="(\/dyn\/opendata\/[^"]+\.html)"/) {
  print STDERR "WARNING: opendata raw html url not found for $url\n";
  exit();
}
$raw_url = "http://www.assemblee-nationale.fr$1";
$opendata_id = $raw_url;
$opendata_id =~ s/^.*opendata\///;

$htmfile = $url;
$htmfile =~ s/\//_/gi;
$htmfile =~ s/\#.*//;
print "$url -> html/$htmfile\n";

open FILE, ">:utf8", "html/$htmfile.tmp";
print FILE $content;
close FILE;
rename "html/$htmfile.tmp", "html/$htmfile";

$a->get($raw_url);
open FILE, ">:utf8", "raw/$opendata_id.tmp";
print FILE $a->content;
close FILE;
rename "raw/$opendata_id.tmp", "raw/$opendata_id";

open FILE, ">:utf8", "raw/$opendata_id.url";
print FILE $raw_url;
close FILE;

print "raw/$opendata_id $url\n";
