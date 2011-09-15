#!/usr/bin/perl

use WWW::Mechanize;
use HTML::TokeParser;
use URI::Escape;
use Encode;

@files = <./html/*>;
$lastfile = pop(@files);
@files = ();

$dannee = "2004"; $dmois = "10";
if ($lastfile =~ /s(\d{4})(\d{2})\d{2}_mono.html/) {
	$dannee = $1;
	$dmois = $2;
}

my ($sec,$min,$hour,$mday,$mon,$year) = localtime(time);

$a = WWW::Mechanize->new();

for($annee = $dannee ; $annee <= $year +1900 ; $annee++) {
$lastmonth = 12;
$lastmonth = $mon if ($year + 1900 == $annee);
for($mois = $dmois ; $mois <= $lastmonth ; $mois++) { 
#    print STDERR "$mois ($lastmonth) $annee ($year)\n";
    $url = 'http://www.senat.fr/seances/s'.sprintf('%04d', $annee).sprintf('%02d', $mois).'/s'.sprintf('%04d', $annee).sprintf('%02d', $mois).'.html';

    print STDERR "search seance in $url\n";

    eval {$a->get($url);};
    next if ($a->status() == 404);
    $content = $a->content;
    $p = HTML::TokeParser->new(\$content);
    $cpt = 0;

    while ($t = $p->get_tag('a')) {
	  next if ($t->[1]{href} !~ /_mono.html/);
	  $href = $t->[1]{href};
          eval {$a->get($href);};
	  if ($@) {
	      print STDERR "error downloading $href\n";
	      $a->back();
	      next;
	  }
          $file = uri_escape($a->uri());
	  open FILE, ">:utf8", "html/$file";
	  $thecontent = $a->content;
	  if ($thecontent =~ s/iso-8859-1/utf-8/gi) {
	      $thecontent = decode("windows-1252", $thecontent);
	  }
	  print FILE $thecontent;
	  close FILE;
	  print "$file\n";
	  $a->back();
    }

}
$dmois = 1; 
}
