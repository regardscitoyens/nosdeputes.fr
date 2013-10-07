#!/usr/bin/perl

use WWW::Mechanize;
use HTML::TokeParser;

$a = WWW::Mechanize->new(autocheck => 0);
$legislature = shift || 14;
$start = shift || '0';
$count = 50;
$ok = 1;
while ($ok) {
    $ok = 0;
    last if ($start > 300);
    $a->post("http://recherche2.assemblee-nationale.fr/resultats_dossiers2.jsp", ['titre' => 'Comptes rendus des réunions', 'text' => '*', 'typeres' => 'crreunions', 'legislature' => $legislature.'ème législature', 'ResultStart' => $start, 'ResultCount' => $count, 'ResultMaxDocs' => 500, 'database' => $legislature.'ComptesRendusReunions', 'fieldtext' => '', 'database' => $legislature.'ComptesRendusReunionsDeleg']);
    $content = $a->content;
    $p = HTML::TokeParser->new(\$content);
    while ($t = $p->get_tag('a')) {
	$txt = $p->get_text('/a');
	if ($txt =~ /compte rendu|e nationale \~/i) {
	    $ok = 1;
	    $curl = $file = $t->[1]{href};
	    $file =~ s/\//_/gi;
	    $file =~ s/\#.*//;
	    $curl =~ s/[^\/]+$//;
            $url{$curl} = 1;
 	    next if -e "html/$file";
	    print "$file\n";
	    $a->get($t->[1]{href});
	    open FILE, ">:utf8", "html/$file.tmp";
	    print FILE $a->content;
	    close FILE;
	    rename "html/$file.tmp", "html/$file";
	    $a->back();
	}
    }
    $start += $count;
}

$a = WWW::Mechanize->new(autocheck => 0);

@url = keys %url;

$lastyear = localtime(time);
$lastyear =~ s/^.*\s(\d{4})$/$1/;
$lastyear++;
$startyear = $legislature * 5 + 1942;
for $year ($startyear .. $lastyear) {
  $session = sprintf('%02d-%02d', $year-2001, $year-2000);
  push(@url, "http://www.assemblee-nationale.fr/$legislature/budget/plf$year/commissions_elargies/cr/");
  push(@url, "http://www.assemblee-nationale.fr/$legislature/cr-mec/$session/index.asp");
  push(@url, "http://www.assemblee-nationale.fr/$legislature/cr-cec/$session/index.asp");
  push(@url, "http://www.assemblee-nationale.fr/$legislature/cr-oecst/$session/index.asp");
  push(@url, "http://www.assemblee-nationale.fr/$legislature/cr-delf/$session/index.asp");
  push(@url, "http://www.assemblee-nationale.fr/$legislature/cr-dom/$session/index.asp");
}
push(@url, "http://www.assemblee-nationale.fr/$legislature/europe/c-rendus/index.asp");
if ($legislature == 13) {
  push(@url, "http://www.assemblee-nationale.fr/13/cr-micompetitivite/10-11/index.asp");
  push(@url, "http://www.assemblee-nationale.fr/13/cr-micompetitivite/11-12/index.asp");
  push(@url, "http://www.assemblee-nationale.fr/13/cr-mitoxicomanie/10-11/index.asp");
  push(@url, "http://www.assemblee-nationale.fr/13/cr-cegrippea/09-10/");
} elsif ($legislature == 14) {
  push (@url, "http://www.assemblee-nationale.fr/14/cr-csprogfinances/11-12/index.asp");
  push (@url, "http://www.assemblee-nationale.fr/14/cr-csprogfinances/12-13/index.asp");
  push (@url, "http://www.assemblee-nationale.fr/14/cr-cesidmet/12-13/index.asp");
  push (@url, "http://www.assemblee-nationale.fr/14/cr-mimage/12-13/index.asp");
  push (@url, "http://www.assemblee-nationale.fr/14/cr-micoutsprod/11-12/index.asp");
  push (@url, "http://www.assemblee-nationale.fr/14/cr-micoutsprod/12-13/index.asp");
  push (@url, "http://www.assemblee-nationale.fr/14/cr-cefugy/12-13/index.asp");
  push (@url, "http://www.assemblee-nationale.fr/14/cr-cefugy/13-14/index.asp");
  push (@url, "http://www.assemblee-nationale.fr/14/cr-cesncm/12-13/index.asp");
  push (@url, "http://www.assemblee-nationale.fr/14/cr-cesncm/13-14/index.asp");
  push (@url, "http://www.assemblee-nationale.fr/14/cr-ceaffcahuzac/12-13/index.asp");
  push (@url, "http://www.assemblee-nationale.fr/14/cr-ceaffcahuzac/13-14/index.asp");
}

$a = WWW::Mechanize->new(autocheck => 0);

foreach $url (@url) {

    $a->get($url);
    $content = $a->content;
    $p = HTML::TokeParser->new(\$content);

    $cpt = 0;

    while ($t = $p->get_tag('a')) {
	$txt = $p->get_text('/a');
	if ($txt =~ /compte rendu|mission/i && $t->[1]{href} =~ /\d\.asp/) {
	    $a->get($t->[1]{href});
	    $file = $a->uri();
	    $file =~ s/\//_/gi;
	    $file =~ s/\#.*//;
            $file =~ s/commissions_elargies_cr_c/commissions_elargies_cr_C/;
	    $size = -s "html/$file";
            if ($size) {
                $cpt++;
                last if ($cpt > 3);
                next;
            }
	    print "$file\n";
	    open FILE, ">:utf8", "html/$file";
	    print FILE $a->content;
	    close FILE;
	    $a->back();
	}
    }
}
