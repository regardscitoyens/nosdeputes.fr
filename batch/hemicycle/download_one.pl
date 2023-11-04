#!/usr/bin/perl

use WWW::Mechanize;
$url = shift;
$url =~ s/^\s+//gi;
$cache = shift;

$a = WWW::Mechanize->new();

$htmfile = $url;
$htmfile =~ s/^https:/http:/;
$htmfile =~ s/\//_/gi;
$htmfile =~ s/\#.*//;

if (!$cache || ! -e "html/$htmfile") {
  open FILE, ">:utf8", "html/$htmfile.tmp";
  $a->get($url);
  $content = $a->content;
  print FILE $content;
  close FILE;
  rename "html/$htmfile.tmp", "html/$htmfile";
} else {
  open(FILE, "html/$htmfile");
  @content = <FILE>;
  $content = "@content";
  close FILE;
}

if ($content =~ /<div class="crs-cr-provisoire">Avertissement: version provisoire/ || $content =~ /---------Cette partie de la s..?ance est en cours de finalisation-------------/) {
  print STDERR "WARNING: skipping compte rendu provisoire for now at $url\n";
  unlink("html/$htmfile");
  exit();
}

if ($content !~ /href="(\/dyn\/opendata\/[^"]+\.xml)"/) {
  print STDERR "WARNING: opendata raw html url not found for $url\n";
  exit();
}

$raw_url = "http://www.assemblee-nationale.fr$1";
$opendata_id = $raw_url;
$opendata_id =~ s/^.*opendata\///;

if (!$cache || ! -e "raw/$opendata_id") {
  $a->get($raw_url);
  open FILE, ">:utf8", "raw/$opendata_id.tmp";
  print FILE $a->content;
  close FILE;
  rename "raw/$opendata_id.tmp", "raw/$opendata_id";
}

print "raw/$opendata_id html/$htmfile $url\n";

