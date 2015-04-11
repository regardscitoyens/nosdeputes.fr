#!/usr/bin/perl
use utf8;
use URI;
use WWW::Mechanize;
use HTML::TokeParser;
$legislature = shift || 14;
$verbose = shift || 0;

sub download_fiche {
	$uri = $file = shift;
	$uri =~ s/^\//http:\/\/www.assemblee-nationale.fr\//;
	print "$uri" if ($verbose);
    $a->max_redirect(0);
	$a->get($uri);
    $status = $a->status();
    if (($status >= 300) && ($status < 400)) {
      $location = $a->response()->header('Location');
      if (defined $location) {
        print "...redirected to $location..." if ($verbose);
        $a->get(URI->new_abs($location, $a->base()));
      }
      $file = $location;
    }
	$file =~ s/^.*\/([^\/]+)/$1/;
	mkdir html unless -e "html/" ;
	open FILE, ">:utf8", "html/$file" || warn("cannot write on html/$file");
	print FILE $a->content;
	close FILE;
	print "\nhtml/$file written\n" if ($verbose);
	return $file;
}
$a = WWW::Mechanize->new();
print "http://www.assemblee-nationale.fr/qui/xml/liste_alpha.asp?legislature=".$legislature."\n" if ($verbose);
$a->get("http://www.assemblee-nationale.fr/qui/xml/liste_alpha.asp?legislature=".$legislature);
$content = $a->content;
$p = HTML::TokeParser->new(\$content);
while ($t = $p->get_tag('a')) {
    if ($t->[1]{class} eq 'dep2') {
	download_fiche($t->[1]{href});
    }
}

open PM, ">finmandats.pm";
print PM '$legislature = '."$legislature;\n";
$a->get("http://www.assemblee-nationale.fr/".$legislature."/tribun/xml/liste_mandats_clos.asp");
$content = $a->content;
$p = HTML::TokeParser->new(\$content);
while ($t = $p->get_tag('td')) {
#    if ($t->[1]{class} eq 'denom') {
	$t = $p->get_tag('a');
	if ($t->[1]{href} && $t->[1]{href} =~ /tribun\/fiches/) {
	    $id = download_fiche($t->[1]{href});
	    $ret = system("grep -i '>Mandat clos<' html/$id > /dev/null");
	    if (! $ret) {
		$t = $p->get_tag('td');
		$t = $p->get_tag('td');
		$t = $p->get_tag('td');
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
#    }
}

