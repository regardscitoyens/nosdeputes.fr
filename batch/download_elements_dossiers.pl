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

#Annee des dossiers à télécharger
$year = shift || $lastyear;
$since_hour = shift || 24;
$verbose = shift || 0;

$yearzero = $year;
if (! $year =~ /^\d{4}$/) {
  print STDERR "Please input a 4-digit year\n";
  exit;
}

%done = ();
%donedo = ();
%donedl = ();
$a = WWW::Mechanize->new();
$aif = WWW::Mechanize->new();
$aif->add_header('If-Modified-Since' => scalar(localtime(time()-3600*$since_hour))) if ($since_hour > 0);

open LISTAMD, ">:utf8", "amendements/to_parse.list";
open LISTDOC, ">:utf8", "documents/to_parse.list";

sub download_one {
  $uri = shift;
  $dir = shift;
  make_path($dir) unless -e $dir;
  return if ($donedl{$uri});
  $donedl{$uri} = 1;
  eval {$aif->get($uri);};
  if ($aif->status() == 404) {
    $aif->back();
    if ($dir =~ /r(ga|ap)/i && $uri =~ /_mono/i) {
      $uri =~ s/_mono//i;
      download_one($uri, $dir);
    } else {
      print STDERR "ERREUR 404 sur $uri\n";
    }
    return;
  }
  $htmfile = uri_escape($aif->uri);
  if ($donedl{$htmfile}) {
    $aif->back();
    return;
  }
  $donedl{$htmfile} = 1;

  $thecontent = $aif->content;
  if (!$thecontent) {
    $aif->back();
    return ;
  }

  if ($dir =~ /amendements/) {
    if ($dir =~ /html/) {
      print LISTAMD "html/$htmfile\n";
    } else {
# gestion des pdfs?
    }
  } else {
    $ssdir = $dir;
    $ssdir =~ s/documents\///;
    print LISTDOC "$ssdir/$htmfile\n";
  }
  open FILE, ">:utf8", "$dir/$htmfile";
  if ($thecontent =~ s/iso-8859-1/utf-8/gi) {
    $thecontent = decode("windows-1252", $thecontent);
  }
  print FILE $thecontent;
  close FILE;
  $aif->back();
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
  $aif->get($urlp);
  if ($donedo{$aif->uri}) {
    return;
  }
  $donedo{$aif->uri} = 1;
  print STDERR " examine dossier $urlp\n" if ($verbose);
  my $contentleg = $aif->content;
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
}

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

close LISTAMD;
close LISTDOC;
