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
    "http://www.assemblee-nationale.fr/$legislature/cri/$session-extra3/",
    "http://www.assemblee-nationale.fr/$legislature/cri/$session-extra2/",
    "http://www.assemblee-nationale.fr/$legislature/cri/$session-extra/",
    "http://www.assemblee-nationale.fr/$legislature/cri/$session/",
    "http://www.assemblee-nationale.fr/$legislature/cri/$oldsession-extra3/",
    "http://www.assemblee-nationale.fr/$legislature/cri/$oldsession-extra2/",
    "http://www.assemblee-nationale.fr/$legislature/cri/$oldsession-extra/",
    "http://www.assemblee-nationale.fr/$legislature/cri/$oldsession/"
);

$a = WWW::Mechanize->new();

foreach $url (@url) {
    $a->get($url);
    $content = $a->content;
    $p = HTML::TokeParser->new(\$content);
    $cpt = 0;
    while ($t = $p->get_tag('a')) {
	$txt = $p->get_text('/a');
	if ($txt =~ /(\d+[\Serm]+\s+\S+ance|S\S+ance uniq)/i && $t->[1]{href} !~ /provisoire/) {
          $a->get($t->[1]{href});
          $file = $a->uri();
          if ($file !~ /provisoire/) {
	    $file =~ s/\//_/gi;
	    $file =~ s/\#.*//;
	    #on ne peut pas quitter dès le premier, seulement au bout de 
	    #trois fois on est sur qu'il n'y a pas de nouveaux fichiers
	    $size = -s "html/$file";
	    if ($size) {
		$cpt++;
		last if ($cpt > 3);
		next;
	    }
	    $cpt = 0;
	    print "$file\n";
	    open FILE, ">:utf8", "html/$file";
	    print FILE $a->content;
	    close FILE;
          }
	  $a->back();
	}
    }
}
