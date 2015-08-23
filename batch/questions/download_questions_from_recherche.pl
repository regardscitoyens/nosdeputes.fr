#!/usr/bin/perl

use utf8;
use Date::Format;
use WWW::Mechanize;
use HTML::TokeParser;
use HTML::Entities;
use URI::Escape;
use Encode;

$date_from = shift || 0;
$verbose = shift || 0;
if ($date_from =~ /^\d{4}$/) {
  $AA = $date_from;
  $date_from .= "0101";
  $today = $AA."1231";
} else {
  if (! $date_from) {
    ($_s,$_m,$_h,$DD,$MM,$AA) = localtime(time-75*24*60*60);
    $MM++;
    $AA += 1900;
    $date_from = sprintf("%04d%02d%02d", $AA, $MM, $DD);
  }
  ($_s,$_m,$_h,$DD,$MM,$AA) = localtime(time+24*60*60);
  $MM++;
  $AA += 1900;
  $today = sprintf("%04d%02d%02d", $AA, $MM, $DD);
}
$date_from =~ s/^(\d{4})-(\d{2})-(\d{2})$/$1$2$3/;
if ($date_from !~ /^\d{8}$/ || $today !~ /^\d{8}$/) {
  print "ERREUR : Mauvais format de date dans download_questions_from_recherche.pl (YYYYMMDD ou YYYY-MM-DD)";
  exit;
}

%done = ();
$count = $count2 = 0;
mkdir "html" unless -e "html/" ;

# aff = ar -> avec réponse ; sr -> sans réponse ; ens -> tous
$aff = "ens";
$baseurl = "http://www.senat.fr/basile/rechercheQuestion.do?&aff=$aff&rch=qs&radio=deau&de=$date_from&au=$today&tri=da&off=";

sub download_one {
  $uri = shift;
  $uri =~ s/^\s+//;
  return if ($done{$uri});
  $done{$uri} = 1;
  $a->get($uri);
  if ( ! $a->success() ) {
    $a->get($uri);
    if ( ! $a->success() ) {
      print "WARNING: could not download $uri\n";
      return;
    }
  }
  $htmfile = uri_escape($a->uri);
  return if ($done{$htmfile});
  $done{$htmfile} = 1;
  print " saving $uri ... " if ($verbose);
  open FILE, ">:utf8", "html/$htmfile";
  $thecontent = $a->content;
  if ($thecontent =~ s/iso-8859-1/utf-8/gi) {
    $thecontent = decode("windows-1252", $thecontent);
  }
  print FILE $thecontent;
  close FILE;
  print "$file downloaded.\n" if ($verbose);
  $a->back();
}

%done = ();
$a = WWW::Mechanize->new( autocheck => 0 );

sub find_questions {
  $start = shift;
  $a->get($baseurl.$start);
  if ( ! $a->success() ) {
    $a->get($baseurl.$start);
    if ( ! $a->success() ) {
      print "WARNING: could not download $uri\n";
      return 0;
    }
  }
  print $baseurl.$start."\n" if ($verbose);
  $content = $a->content;
  if ($content =~ s/iso-8859-1/utf-8/gi) {
    $content = decode("windows-1252", $content);
  }
  $content = decode_entities($content);
  $content =~ s/\s+/ /g;
  $content =~ s/<a/\n<a/g;
  $total = $1 if ($content =~ /\[(\d+) (r[^\]]+ponse|question)s?\]/);
  foreach $line (split /\n/, $content) {
    if ($line =~ /<a href="visio\.do\?id=qSEQ(\d{2})(\d{2})(\d{4}\w)&/) {
      $anneeshort = $1;
      $mois = $2;
      $id = $3;
      $annee = $anneeshort + 1900;
      if ($annee < 1975) {
        $annee += 100
      }
      $url = "http://www.senat.fr/questions/base/$annee/qSEQ".$anneeshort.$mois.$id.".html";
      $count++;
      download_one($url);
    }
  }
  $a->back();
  return $total;
}
$page = 0;
$total = find_questions($page*10);
while ($page < int($total/10)) {
  $page++;
  print "\nPage $page : $count/$total : " if ($verbose);
  find_questions($page*10);
}
print "$count questions téléchargées\n\n" if ($verbose);

open(FILE, 'liste_sans_reponse.txt') ;
@string = <FILE>;
$string = "@string";
close FILE;
foreach $url (split /\n/, $string) {
  $count2++;
  download_one($url);
}
print $count2." questions sans réponse retéléchargées\n\n" if ($verbose);
print $count+$count2." questions téléchargées\n" if ($verbose);

