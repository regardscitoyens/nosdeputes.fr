#!/usr/bin/perl

use WWW::Mechanize;
use HTML::TokeParser;
$legislature = shift || 14;
$verbose = shift || 0;

sub download_fiche {
	$uri = $file = shift;
	$file =~ s/^.*\/([^\/]+)/$1/;
	print "$file\n" if ($verbose);
	$a->get($uri);
	mkdir html unless -e "html/" ;
	open FILE, ">", "html/$file";
	print FILE $a->content;
	close FILE;
	return $file;
}
$a = WWW::Mechanize->new();

$a->get("http://www.assemblee-nationale.fr/qui/xml/liste_alpha.asp?legislature=".$legislature);
$content = $a->content;
$p = HTML::TokeParser->new(\$content);
while ($t = $p->get_tag('a')) {
    if ($t->[1]{class} eq 'dep2') {
	download_fiche($t->[1]{href});
    }
}

$a->get("http://www.assembleenationale.fr/".$legislature."/tribun/xml/liste_mandats_clos.asp");
$content = $a->content;
$p = HTML::TokeParser->new(\$content);
open PM, ">finmandats.pm";
while ($t = $p->get_tag('td')) {
    if ($t->[1]{class} eq 'denom') {
	$t = $p->get_tag('a');
	if ($t->[1]{href}) {
	    $id = download_fiche($t->[1]{href});
	    $ret = system("grep -i '>Mandat clos<' html/$id > /dev/null");
	    if (! $ret) {
		$t = $p->get_tag('td');
		$t = $p->get_tag('td');
		$t = $p->get_tag('td');
		$t = $p->get_text('/td');
		$t =~ s/[^\d\/]//g;
              if ($legislature == 13) {
# Cas Estrosi dont la fin de mandat n'est pas mise à jour sur la page de l'AN
                if ($id == 1263) {
                  $t = '23/07/2009';
                }
# Cas Poisson dont la fin de mandat n'est pas mise à jour sur la page de l'AN
                if ($id == 345937) {
                  $t = '20/05/2010';
                }
# Cas Bertrand dont la fin de mandat n'est pas mise à jour sur la page de l'AN
                if ($id == 267080) {
                  $t = '14/12/2010';
                }
# Cas Ginesy dont la fin de mandat n'est pas mise à jour sur la page de l'AN
                if ($id == 267680) {
                  $t = '13/12/2010';
                }
              }
		print PM "\$fin_mandat{'$id'} = '$t';\n";
	    }
	}
    }
}
