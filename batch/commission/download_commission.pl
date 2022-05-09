#!/usr/bin/perl

use WWW::Mechanize;
use HTML::TokeParser;

$legislature = shift || 15;

$a = WWW::Mechanize->new(autocheck => 0);
$b = WWW::Mechanize->new(autocheck => 0);
$page = 0;
while ($page < 20) {
  $offset = 10 * $page;
  $url = "http://www2.assemblee-nationale.fr/recherche/resultats_recherche/(offset)/$offset/(tri)/date/(legislature)/$legislature/(query)/eyJxIjoidHlwZURvY3VtZW50OlwiY29tcHRlIHJlbmR1XCIgYW5kIGNvbnRlbnU6YSIsInJvd3MiOjEwLCJzdGFydCI6MCwid3QiOiJwaHAiLCJobCI6ImZhbHNlIiwiZmwiOiJ1cmwsdGl0cmUsdXJsRG9zc2llckxlZ2lzbGF0aWYsdGl0cmVEb3NzaWVyTGVnaXNsYXRpZix0ZXh0ZVF1ZXN0aW9uLHR5cGVEb2N1bWVudCxzc1R5cGVEb2N1bWVudCxydWJyaXF1ZSx0ZXRlQW5hbHlzZSxtb3RzQ2xlcyxhdXRldXIsZGF0ZURlcG90LHNpZ25hdGFpcmVzQW1lbmRlbWVudCxkZXNpZ25hdGlvbkFydGljbGUsc29tbWFpcmUsc29ydCIsInNvcnQiOiIifQ==";
  $page++;

  $a->post($url);
  $content = $a->content;
  $p = HTML::TokeParser->new(\$content);
  while ($t = $p->get_tag('a')) {
    $txt = $p->get_text('/a');
    $source_url = $t->[1]{href};
    $source_url =~ s/(^[\s\t]+|[\s\t]+$)//g;
    $file = $source_url;
    if ($txt =~ /compte rendu|e nationale \~/i && $source_url !~ /(nale\.fr\/dyn\/c\d+\.asp|\/cri\/(2|congres)|\(typeDoc\))/ && $source_url =~ /nationale\.fr\/$legislature\//) {

      $file =~ s/https:/http:/;
      $file =~ s/\//_/gi;
      $file =~ s/\#.*//;
      next if -e "html/$file";

      $b->get($source_url);
      $text = $b->content;

      if ($text !~ /href="(\/dyn\/opendata\/[^"]+\.html)"/) {
        print STDERR "WARNING: opendata raw html url not found for $source_url\n";
        next;
      }
      $raw_url = "http://www.assemblee-nationale.fr$1";
      $opendata_id = $raw_url;
      $opendata_id =~ s/^.*opendata\///;
      next if -e "raw/$opendata_id";

      open FILE, ">:utf8", "html/$file.tmp";
      print FILE $text;
      close FILE;
      rename "html/$file.tmp", "html/$file";

      $b->get($raw_url);
      open FILE, ">:utf8", "raw/$opendata_id.tmp";
      print FILE $b->content;
      close FILE;
      rename "raw/$opendata_id.tmp", "raw/$opendata_id";
      open FILE, ">:utf8", "raw/$opendata_id.url";
      print FILE $raw_url;
      close FILE;

      print "raw/$opendata_id html/$file $source_url\n";
    }
  }
}

exit(0);


# Deprecated code for old legislatures since april 2020
@url = keys %url;

$lastyear = localtime(time);
$lastyear =~ s/^.*\s(\d{4})$/$1/;
$lastyear++;
$startyear = $legislature * 5 + 1942;
for $year ($startyear .. $lastyear) {
  $session = sprintf('%02d-%02d', $year-2001, $year-2000);
  push(@url, "http://www.assemblee-nationale.fr/$legislature/cr-cedu/$session/index.asp");
  push(@url, "http://www.assemblee-nationale.fr/$legislature/cr-eco/$session/index.asp");
  push(@url, "http://www.assemblee-nationale.fr/$legislature/cr-cafe/$session/index.asp");
  push(@url, "http://www.assemblee-nationale.fr/$legislature/cr-soc/$session/index.asp");
  push(@url, "http://www.assemblee-nationale.fr/$legislature/cr-cdef/$session/index.asp");
  push(@url, "http://www.assemblee-nationale.fr/$legislature/cr-dvp/$session/index.asp");
  push(@url, "http://www.assemblee-nationale.fr/$legislature/cr-cfiab/$session/index.asp");
  push(@url, "http://www.assemblee-nationale.fr/$legislature/cr-cloi/$session/index.asp");
  push(@url, "http://www.assemblee-nationale.fr/$legislature/budget/plf$year/commissions_elargies/cr/");
  push(@url, "http://www.assemblee-nationale.fr/$legislature/cr-mec/$session/index.asp");
  push(@url, "http://www.assemblee-nationale.fr/$legislature/cr-mecss/$session/index.asp");
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
  push (@url, "http://www.assemblee-nationale.fr/14/cr-csprostit/13-14/index.asp");
  push (@url, "http://www.assemblee-nationale.fr/14/cr-cesidmet/12-13/index.asp");
  push (@url, "http://www.assemblee-nationale.fr/14/cr-mimage/12-13/index.asp");
  push (@url, "http://www.assemblee-nationale.fr/14/cr-micoutsprod/11-12/index.asp");
  push (@url, "http://www.assemblee-nationale.fr/14/cr-micoutsprod/12-13/index.asp");
  push (@url, "http://www.assemblee-nationale.fr/14/cr-misimplileg/13-14/index.asp");
  push (@url, "http://www.assemblee-nationale.fr/14/cr-miecotaxe/13-14/index.asp");
  push (@url, "http://www.assemblee-nationale.fr/14/cr-cefugy/12-13/index.asp");
  push (@url, "http://www.assemblee-nationale.fr/14/cr-cefugy/13-14/index.asp");
  push (@url, "http://www.assemblee-nationale.fr/14/cr-cesncm/12-13/index.asp");
  push (@url, "http://www.assemblee-nationale.fr/14/cr-cesncm/13-14/index.asp");
  push (@url, "http://www.assemblee-nationale.fr/14/cr-ceaffcahuzac/12-13/index.asp");
  push (@url, "http://www.assemblee-nationale.fr/14/cr-ceaffcahuzac/13-14/index.asp");
  push (@url, "http://www.assemblee-nationale.fr/14/cr-cenucleaire/13-14/index.asp");
} elsif ($legislature == 15) {
  push (@url, "http://www.assemblee-nationale.fr/15/cr-miaidenf/18-19/index.asp");
}

$a = WWW::Mechanize->new(autocheck => 0);
foreach $url (@url) {
  $cpt = 0;
  $a->get($url);
  $content = $a->content;
  $p = HTML::TokeParser->new(\$content);
  while ($t = $p->get_tag('a')) {
    $txt = $p->get_text('/a');
    if ($txt =~ /compte rendu|mission/i && $t->[1]{href} =~ /\d\.asp/) {
      $a->get($t->[1]{href});
      $file = $a->uri();
      $file =~ s/(^[\s\t]+|[\s\t]+$)//g;
      $file =~ s/\//_/gi;
      $file =~ s/\#.*//;
      #$file =~ s/commissions_elargies_cr_c/commissions_elargies_cr_C/;
      $file =~ s/commissions_elargies_cr_C/commissions_elargies_cr_c/;
      $size = -s "html/$file";
      if ($size) {
        $cpt++;
        #last if ($cpt > 3);
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
