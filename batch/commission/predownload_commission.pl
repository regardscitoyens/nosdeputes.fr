#!/usr/bin/perl


use WWW::Mechanize;
use HTML::TokeParser;
use  URI::Escape;
use Encode;

$with_archive = shift;

@archive = ("http://www.senat.fr/compte-rendu-commissions/commission-mixte-paritaire_archives.html", "http://www.senat.fr/compte-rendu-commissions/affaires-etrangeres_archives.html", "http://www.senat.fr/compte-rendu-commissions/controle-de-la-securite-sociale_archives.html", "http://www.senat.fr/compte-rendu-commissions/affaires-sociales_archives.html", "http://www.senat.fr/compte-rendu-commissions/culture_archives.html","http://www.senat.fr/compte-rendu-commissions/economie_archives.html",  "http://www.senat.fr/compte-rendu-commissions/finances_archives.html", "http://www.senat.fr/compte-rendu-commissions/lois_archives.html", "http://www.senat.fr/compte-rendu-commissions/delegation-aux-droits-des-femmes_archives.html", "http://www.senat.fr/europe/reunions/archives.html");

@indexes = ("http://www.senat.fr/offices_deleg_observatoire/index.html", "http://www.senat.fr/commission/spec/index.html", "http://www.senat.fr/commission/missions/index.html", "http://www.senat.fr/commission/enquete/index.html");

@more = ("http://www.senat.fr/compte-rendu-commissions/office-parlementaire-d-evaluation-des-choix-scient.-tech..html", "http://www.senat.fr/compte-rendu-commissions/controle-de-la-securite-sociale-mecss.html", "http://www.senat.fr/europe/reunions.html");

$a = WWW::Mechanize->new();
$start = shift || '0';
$count = 50;
$ok = 1;

foreach $index (@indexes) {
    print "INDEX : $index\n";
    $a->get($index);
    $content = $a->content;
    $p = HTML::TokeParser->new(\$content);
    while ($t = $p->get_tag('a')) {
	if ($t->[1]{href} =~ /\/commission\/([^\/]+)\/(.*)/) {
	    $type = $1 ; $suite = $2;
	    next if ($suite =~ /^index.html/ && $type =~ /\/(spec|enquete)\//);
	    $url = $t->[1]{href};
	    $url =~ s/\#.*//;
	    $url =~ s/\/\.\.\/commission\//\/commission\//;
	    next if ($done{$url});
	    $done{$url} = 1;
	    print "MISSION : $url\n";
	    $a->get($url);
	    $content_page = $a->content;
	    $pp = HTML::TokeParser->new(\$content_page);
	    while ($tp = $pp->get_tag('a')) {
		$text = $pp->get_text('/a');
		$href = $tp->[1]{href};
#		print "LOG: ".$href." $text\n";
		$cr = '';
		$cr = $href if (($text =~ /Comptes[\s\-]+rendus$/i || $href =~ /compte-rendu-commissions/ || $href =~ /\/travaux.html/) && $href !~ /\d{6}/ && $href !~ /somsea/ && $href !~ /prog.html/) ;
		$cr = $url if ($href =~ /\d{6}.html/ && $href !~ /^\/presse\//);
		next unless ($cr);
		$cr =~ s/^\/..\//\//;
		if ($cr !~ /^http/ && $cr !~ /^\//) {
		    $a->get($cr);
		    $cr = $a->uri();
		    $a->back();
		}
		$cr = "http://www.senat.fr$cr" if ($cr =~ /^\//);
		$cr =~ s/\#.*//;
		next if ($cr{$cr});
		print "CR FOUND: $cr\n";
		$cr{$cr} = 1;
	    }
	    $a->back();
	}
    }
}

if ($with_archive) {
    @more = (@more, @archive);
}

mkdir('conf') if (! -e 'conf');
@ignores = "";
if (-e 'conf/ignore.list') {
  open FILE, "conf/ignore.list";
  @ignores = <FILE>;
  close FILE;
}

open FILE, "> conf/cr.list";
foreach $url (@more) {
  $skip = 0;
  foreach $ignore (@ignores) {
    chomp($ignore);
    if ($ignore eq $url) {
      $skip = 1;
      last;
    }
  }
  next if ($skip);
  print FILE "$url\n";
}
foreach $url (keys %cr) {
  $skip = 0;
  foreach $ignore (@ignores) {
    chomp($ignore);
    if ($ignore eq $url) {
      $skip = 1;
      last;
    }
  }
  next if ($skip);
  print FILE "$url\n";
}
close FILE;
