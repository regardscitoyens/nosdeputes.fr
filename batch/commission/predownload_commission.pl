#!/usr/bin/perl


use WWW::Mechanize;
use HTML::TokeParser;
use  URI::Escape;
use Encode;

$with_archive = shift;

@archive = ("https://www.senat.fr/compte-rendu-commissions/commission-mixte-paritaire_archives.html", "https://www.senat.fr/compte-rendu-commissions/affaires-etrangeres_archives.html", "https://www.senat.fr/compte-rendu-commissions/controle-de-la-securite-sociale_archives.html", "https://www.senat.fr/compte-rendu-commissions/affaires-sociales_archives.html", "https://www.senat.fr/compte-rendu-commissions/culture_archives.html","https://www.senat.fr/compte-rendu-commissions/economie_archives.html",  "https://www.senat.fr/compte-rendu-commissions/finances_archives.html", "https://www.senat.fr/compte-rendu-commissions/lois_archives.html", "https://www.senat.fr/compte-rendu-commissions/delegation-aux-droits-des-femmes_archives.html", "https://www.senat.fr/europe/reunions/archives.html");

@indexes = ("https://www.senat.fr/offices_deleg_observatoire/index.html", "https://www.senat.fr/commission/spec/index.html", "https://www.senat.fr/commission/missions/index.html", "https://www.senat.fr/commission/enquete/index.html");

@more = ("https://www.senat.fr/compte-rendu-commissions/office-parlementaire-d-evaluation-des-choix-scient.-tech..html", "https://www.senat.fr/compte-rendu-commissions/controle-de-la-securite-sociale-mecss.html", "https://www.senat.fr/europe/reunions.html", "https://www.senat.fr/compte-rendu-commissions/groupe-de-suivi-brexit.html", "https://www.senat.fr/compte-rendu-commissions/groupe-de-suivi-brexit_archives.html");

$a = WWW::Mechanize->new();
$start = shift || '0';
$count = 50;
$ok = 1;

foreach $index (@indexes) {
    print "INDEX : $index\n";
    eval { $a->get($index); };
    if( not $a->res->is_success ){
      print STDERR "ERREUR geting $index, retrying...";
      eval { $a->get($index); };
      if( not $a->res->is_success ){
        print STDERR " ...still failing. skipping it\n";
        next;
      }
      print STDERR "\n";
    }
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
	    eval { $a->get($url); };
        if( not $a->res->is_success ){
          print STDERR "ERREUR geting $url, retrying...";
          eval { $a->get($url); };
          if( not $a->res->is_success ){
            print STDERR " ...still failing. skipping it\n";
            next;
          }
          print STDERR "\n";
        }

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
		$cr = "https://www.senat.fr$cr" if ($cr =~ /^\//);
		$cr =~ s/\#.*//;
		$cr =~ s/^http:/https:/;
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
