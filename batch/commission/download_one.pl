#!/usr/bin/perl

use WWW::Mechanize;

$url = shift;
$url =~ s/^\s+//i;
$cache = shift;

$oldstyle = 0;
if ($url =~ /^https:\/\/www2.assemblee-nationale.fr/) {
  $oldstyle = 1;
}

$a = WWW::Mechanize->new();
$a->get($url);
$content = $a->content;

if (!$oldstyle) {
  $retry = 10;
  while ($retry > 0 && $content !~ /href="(\/dyn\/opendata\/[^"]+\.html)"/) {
    sleep 5;
    $a->get($url);
    $content = $a->content;
  }
  if ($content !~ /href="(\/dyn\/opendata\/[^"]+\.html)"/) {
    print STDERR "WARNING: opendata raw html url not found for $url\n";
    exit();
  }
}

$raw_url = "https://www.assemblee-nationale.fr$1";
$opendata_id = $raw_url;
$opendata_id =~ s/^.*opendata\///;

$htmfile = $url;
$htmfile =~ s/\//_/gi;
$htmfile =~ s/\#.*//;

open FILE, ">:utf8", "html/$htmfile.tmp";
print FILE $content;
close FILE;
rename "html/$htmfile.tmp", "html/$htmfile";

if (!$cache || ! -e "raw/$opendata_id") {
  $a->get($raw_url);
  open FILE, ">:utf8", "raw/$opendata_id.tmp";
  print FILE $a->content;
  close FILE;
  rename "raw/$opendata_id.tmp", "raw/$opendata_id";
}

open FILE, ">:utf8", "raw/$opendata_id.url";
print FILE $raw_url;
close FILE;

print "raw/$opendata_id html/$htmfile $url\n";
