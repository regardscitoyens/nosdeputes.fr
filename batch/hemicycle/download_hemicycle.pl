#!/usr/bin/perl

use WWW::Mechanize;
use HTML::TokeParser;

$legislature = shift || 14;
$lastyear = localtime(time);
my @month = `date +%m`;
$lastyear =~ s/^.*\s(\d{4})$/$1/;
$lastyear-- if ($month[0] < 10);
$session = "$lastyear-".($lastyear+1);
$oldsession = ($lastyear-1)."-$lastyear";

@url = (
    "https://www.assemblee-nationale.fr/$legislature/cri/$session-extra3/",
    "https://www.assemblee-nationale.fr/$legislature/cri/$session-extra2/",
    "https://www.assemblee-nationale.fr/$legislature/cri/$session-extra/",
    "https://www.assemblee-nationale.fr/$legislature/cri/$session/",
    "https://www.assemblee-nationale.fr/$legislature/cri/$oldsession-extra3/",
    "https://www.assemblee-nationale.fr/$legislature/cri/$oldsession-extra2/",
    "https://www.assemblee-nationale.fr/$legislature/cri/$oldsession-extra/",
    "https://www.assemblee-nationale.fr/$legislature/cri/$oldsession/",
    "https://www.assemblee-nationale.fr/$legislature/cri/congres/"
);

$a = WWW::Mechanize->new(autocheck => 0);

foreach $url (@url) {
  $a->get($url);
  $content = $a->content;
  $p = HTML::TokeParser->new(\$content);
  $cpt = 0;
  while ($t = $p->get_tag('a')) {
    if ($t->[1]{href} =~ /^(https?:\/\/www\.assemblee-nationale\.fr\/$legislature\/cri\/\d+-\d+[\-extra\d]*\/)?\d+\.asp$/) {
      $a->get($t->[1]{href});
      $file = $a->uri();
      $file =~ s/^https:/http:/;
      if ($file !~ /provisoire|2017-2018\/2013/) {
	    $file =~ s/\//_/gi;
	    $file =~ s/\#.*//;
	    $newcontent = $a->content;
	    $a->back();
	    #on ne peut pas quitter dès le premier, seulement au bout de
	    #trois fois on est sur qu'il n'y a pas de nouveaux fichiers
	    $size = -s "html/$file";
	    if ($size) {
            $cpt++;
            last if ($cpt > 3);
            next;
	    }
	    $cpt = 0;
	    if ($newcontent !~ /(cours de finalisation\s*----|version provisoire mise en ligne)/) {
	        print "$file\n";
	        open FILE, ">:utf8", "html/$file";
	        print FILE $newcontent;
	        close FILE;
        }
      }
	}
  }
}
