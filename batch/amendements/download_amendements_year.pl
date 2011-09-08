#!/usr/bin/perl

use WWW::Mechanize;
use HTML::TokeParser;
use URI::Escape;
use Encode;

$lastyear = localtime(time);
my @month = `date +%m`;
$lastyear =~ s/^.*\s(\d{4})$/$1/;
$lastyear-- if ($month[0] < 10);

$year = shift;
$year = $lastyear if (!$year);
if (! $year =~ /^\d{4}$/) {
  print STDERR "Please input a 4-digit year\n";
  exit;
}

$verbose = shift || 0;

mkdir "html"  unless -e "html/" ;
mkdir "pdfs"  unless -e "pdfs/" ;

%done = ();
$a = WWW::Mechanize->new();

sub download_amendements {
  $uri = shift;
  $dir = shift;
  return if ($done{$uri});
  $done{$uri} = 1;
  eval {$a->get($uri);};
  if ($a->status() == 404) {
    print STDERR "ERREUR 404 sur $uri\n";
    $a->back();
    return;
  }
  $htmfile = uri_escape($a->uri);
  if ($done{$htmfile}) {
    $a->back();
    return;
  }
  $done{$htmfile} = 1;
  print "$htmfile\n";
  open FILE, ">:utf8", "$dir/$htmfile";
  $thecontent = $a->content;
  if ($thecontent =~ s/iso-8859-1/utf-8/gi) {
    $thecontent = decode("windows-1252", $thecontent);
  }
  print FILE $thecontent;
  close FILE;
  $a->back();
}

sub find_amendements {
  $urlp = shift;
  return if ($done{$urlp});
  $done{$urlp} = 1;
  $a->get($urlp);
  if ($done{$a->uri}) {
    $a->back(); 
    return;
  }
  $done{$a->uri} = 1;
  print STDERR " examine dossier $urlp\n" if ($verbose);
  $contentleg = $a->content;
  if ($contentleg =~ s/iso-8859-1/utf-8/gi) {
    $contentleg = decode("windows-1252", $contentleg);
  }
  $contentleg =~ s/<a/\n<a/g;
  foreach $line (split /\n/, $contentleg) {
    $urla = "";
    if ($line =~ /<a([^>]+)?\s+href\s?=\s?['"]\s?[^'"]*(\/amendements[^'"]+(\d{4})-[^'"]+\/)accueil\.html['"]/) {
      if ($3 < 2004) {
        $a->back();
        next;
      }
      $urla = "http://www.senat.fr$2jeu_complet.html";
      $outdir = "html";
    } elsif ($line =~ /<a([^>]+)?\s+href\s?=\s?['"]\s?[^'"]*(\/amendements[^'"]+\.pdf)['"]/){
      $urla = "http://www.senat.fr$2";
      $outdir = "pdfs";
    }
    download_amendements($urla, $outdir) if ($urla);
  }
  $a->back();
}

while ($year <= $lastyear) {
  $baseurl = "http://www.senat.fr/dossiers-legislatifs/depots/depots-$year.html";
  print STDERR "Download amendements de la session $year : $baseurl ...\n" if ($verbose);
  $a->get($baseurl);
  $content = $a->content;
  if ($content =~ s/iso-8859-1/utf-8/gi) {
    $content = decode("windows-1252", $content);
  }
  $content =~ s/<a/\n<a/g;
  foreach $line (split /\n/, $content) {
    if ($line =~ /<a([^>]+)?\s+href\s?=\s?['"]\s?([^'"]*\/dossierleg\/[^'"]+)['"]/) {
      $url = $2;
      $url =~ s/^\//http:\/\/www.senat.fr\//i;
      find_amendements($url);
    }
  }
  $a->back();
  $year++;
}

