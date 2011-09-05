#!/usr/bin/perl

use WWW::Mechanize;
use HTML::TokeParser;
use URI::Escape;
use Encode;

@files = <./html/*>;
$lastfile = pop(@files);
@files = ();

$dannee = "2005"; $dmois = "10";
if ($lastfile =~ /s(\d{4})(\d{2})\d{2}_mono.html/) {
	$dannee = $1;
	$dmois = $2;
}

my ($sec,$min,$hour,$mday,$mon,$year) = localtime(time);

$a = WWW::Mechanize->new();

print "$dannee : $year\n";
print "$dmois ; $mon\n";

for($annee = $dannee ; $annee <= $year +1900 ; $annee++) {
$lastmonth = 12;
$lastmonth = $mon if ($year + 1900 == $annee);
for($mois = $dmois ; $mois <= $lastmonth ; $mois++) { 

    $url = "http://www.senat.fr/seances/s$annee$mois/s$annee$mois.html";

    print STDERR "$url\n";

    $a->get($url);
    $content = $a->content;
    $p = HTML::TokeParser->new(\$content);
    $cpt = 0;
    while ($t = $p->get_tag('a')) {
	  next if ($t->[1]{href} !~ /_mono.html/);
          $a->get($t->[1]{href});
          $file = uri_escape($a->uri());
	  print "$file\n";
	  open FILE, ">:utf8", "html/$file";
	  print FILE $a->content;
	  close FILE;
	  $a->back();
    }

}
$dmois = 1; 
}
