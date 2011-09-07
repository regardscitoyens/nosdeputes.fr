#!/usr/bin/perl

use WWW::Mechanize;
use HTML::TokeParser;
use URI::Escape;
use Encode;

$outdir = shift || "html";
if (! $outdir =~ /(\d{4}|html)/) {
  print "Please input a 4-digit year\n";
  exit;
}
$verbose = shift || 0;
$count = 0;
mkdir $outdir unless -e "$outdir/" ;

if ($outdir == "html") {
  $annee = localtime(time);
  $annee =~ s/^.*\s(\d{4})$/$1/;
} else {
  $annee = $outdir;
}
$baseurl = "http://www.senat.fr/questions/base/$annee/";
print "Download questions from $annee : $baseurl ...\n" if ($verbose);

$a = WWW::Mechanize->new();
$a->get($baseurl);
$content = decode("windows-1252", $a->content);
$content =~ s/<a/\n<a/g;

foreach $line (split /\n/, $content) {
  if ($line =~ /<a([^>]+)?\s+href\s?=\s?['"]\s?([^'"]+questions\/base\/$annee\/qSEQ[^'"]+)['"]/) {
    $url = $2;
    $count++;
    $a->get($url);
    $file = uri_escape($a->uri());
    print " saving http://www.senat.fr$url ... " if ($verbose);
    open FILE, ">:utf8", "$outdir/$file";
    $thecontent = $a->content;
    if ($thecontent =~ s/iso-8859-1/utf-8/gi) {
      $thecontent = decode("windows-1252", $thecontent);
    }
    print FILE $thecontent;
    close FILE;
    print "$file downloaded.\n" if ($verbose);
    $a->back();
  }
}
print "$count questions téléchargées\n\n" if ($verbose);

