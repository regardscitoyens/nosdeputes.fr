#!/usr/bin/perl

use WWW::Mechanize;
use HTML::TokeParser;
use URI::Escape;
use Encode;
use File::Path qw(make_path);

$lastyear = localtime(time);
my @month = `date +%m`;
$lastyear =~ s/^.*\s(\d{4})$/$1/;
$lastyear-- if ($month[0] < 10);

$year = shift;
$year = $lastyear if (!$year);
$yearzero = $year;
if (! $year =~ /^\d{4}$/) {
  print STDERR "Please input a 4-digit year\n";
  exit;
}

$verbose = shift || 0;

%done = ();
%donedo = ();
%donedl = ();
$a = WWW::Mechanize->new();

sub download_one {
  $uri = shift;
  $dir = shift;
  make_path($dir) unless -e $dir;
  return if ($donedl{$uri});
  $donedl{$uri} = 1;
  eval {$a->get($uri);};
  if ($a->status() == 404) {
    $a->back();
    if ($dir =~ /r(ga|ap)/i && $uri =~ /_mono/i) {
      $uri =~ s/_mono//i;
      download_one($uri, $dir);
    } else {
      print "ERREUR 404 sur $uri\n";
    }
    return;
  }
  $htmfile = uri_escape($a->uri);
  if ($donedl{$htmfile}) {
    $a->back();
    return;
  }
  $donedl{$htmfile} = 1;
  print "    $dir\t\t->\t\t$htmfile\n";
  open FILE, ">:utf8", "$dir/$htmfile";
  $thecontent = $a->content;
  if ($thecontent =~ s/iso-8859-1/utf-8/gi) {
    $thecontent = decode("windows-1252", $thecontent);
  }
  print FILE $thecontent;
  close FILE;
  $a->back();
}

sub examine_url {
  my $lk = lc(shift);
  return if ($lk !~ /<a([^>]+)?\s+href\s?=\s?['"]\s?([^'"#]+)['"#].*$/i);
  $lk = $2;
  $lk =~ s/^([^h\/])/\/$1/i;
  return "" if ($lk !~ /senat\.fr/ && $lk !~ /^\//);
  $lk =~ s/^.*senat\.fr//i;
  $lk =~ s/^/http:\/\/www.senat.fr/;
  return if ($done{$lk});
  $done{$lk} = 1;
  my $urls = "";
  my $outdirs = "";
  if ($lk =~ /\/(motion)?(tas|p[jp][lr])(\d\d)-(\d{3})/) {
    $y = $3 + 2000;
    return $lk if ($y lt $yearzero || $y gt 2070);
    $urls = "http://www.senat.fr/leg/$1$2$3-$4.html";
    $outdirs = "documents/$2";
  } elsif ($lk =~ /\/([arl])(\d\d)(-\d{3}\d*(-\d+)*)/) {
    $y = $2 + 2000;
    return $lk if ($y lt $yearzero || $y gt 2070);
    $urls = "http://www.senat.fr/rap/$1$2$3/$1$2$3_mono.html";
    $outdirs = "documents/rap";
  } elsif ($lk =~ /\/(\d{4})\/(ga\d+)-/) {
    $y = $1;
    return $lk if ($y lt $yearzero || $y gt 2070);
    $urls = "http://www.senat.fr/ga/$2/$2_mono.html";
    $outdirs = "documents/rga";
  }
  download_one($urls, $outdirs) if $urls;
  return $lk;
}

sub find_elements {
  my $urlp = shift;
  my $urla = "";
  my $outdir = "";
  $a->get($urlp);
  if ($donedo{$a->uri}) {
    $a->back();
    return;
  }
  $donedo{$a->uri} = 1;
  print STDERR " examine dossier $urlp\n" if ($verbose);
  my $contentleg = $a->content;
  if ($contentleg =~ s/iso-8859-1/utf-8/gi) {
    $contentleg = decode("windows-1252", $contentleg);
  }
  
  $contentleg =~ s/<a/\n<a/ig;
  foreach $line (split /\n/, $contentleg) {
   if ($line =~ /^<a/) {
    $line = examine_url($line);
    $urla = "";
    if ($line =~ /(\/amendements.*\/(\d{4})-.+\/)accueil\.html/) {
      if ($2 lt $yearzero) {
        $a->back();
        next;
      }
      $urla = "http://www.senat.fr$1jeu_complet.html";
      $outdir = "amendements/html";
    } elsif ($line =~ /(\/amendements[^'"]+\.pdf)/){
      $urla = $line;
      $outdir = "amendements/pdfs";
    }
    download_one($urla, $outdir) if ($urla);
   }
  }
  $a->back();
}

# urls en compte-rendu-commissions a checker si already dl?

sub explore_page {
  my $baseurls = shift;
  $a->get($baseurls);
  my $content = $a->content;
  if ($content =~ s/iso-8859-1/utf-8/gi) {
    $content = decode("windows-1252", $content);
  }
  $content =~ s/<a/\n<a/ig;
  foreach $link (split /\n/, $content) {
   if ($link =~ /^<a/) {
    $link = examine_url($link);
    if ($link =~ /dossier(leg|-legislatif)/) {
      find_elements($link);
    }
   }
  }
  $a->back();
}

while ($year <= $lastyear) {
  $baseurl = "http://www.senat.fr/dossiers-legislatifs/depots/depots-$year.html";
  print STDERR "Download documents et amendements de la session $year : $baseurl ...\n" if ($verbose);
  explore_page($baseurl);
  $year++;
}

print STDERR "Download rapports from groupes d'amitié ...\n" if ($verbose);
explore_page("http://www.senat.fr/rapports/rapports-groupe-amitie.html");

