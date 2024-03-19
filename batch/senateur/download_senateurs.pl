#!/usr/bin/perl
use WWW::Mechanize;
use HTML::TokeParser;
use URI::Escape;
use Encode;


$|=1;

$verbose = shift || 0;
%done = ();
$done{'/senateur/ginesta_georges15447f.html'} = 1;
$done{'/senateur/dubois_emile000322.html'} = 1;
sub download_fiche {
	$uri = shift;
	return if ($done{$uri});
	$done{$uri} = 1;
  	eval {
		$a->get("https://web.archive.org/web/20230101000000/https://www.senat.fr".$uri);
		1;
	} or return "ERROR: "."$@";
	$file = uri_escape($a->uri());
	return if ($done{$file});
	$done{$file} = 1;
        print "saving " if ($verbose);
	print $file;
	print " : " if ($verbose);
	mkdir html unless -e "html/" ;
	open FILE, ">:utf8", "html/$file";
	$thecontent = $a->content;
	if ($thecontent =~ s/iso-8859-1/utf-8/ig) {
	    $thecontent = decode("windows-1252", $thecontent);
	}
    $thecontent =~ s/<li>Membre ([^<]+<a href="[^>]+>[^<]+<\/a>)\s*\((Pr..?sidente?)\)<\/li>/<li>\2 \1<\/li>/ig;
	print FILE $thecontent;
	close FILE;
        print "DONE" if ($verbose);
	print "\n";
	return $file;
}
$a = WWW::Mechanize->new();

sub find_senateurs {
	$url = shift;
	print "looking for senateurs in $url\n" if ($verbose);
	eval { $a->get($url); };
    if ($a->status() == 404) {
        return;
    }
	$content = $a->content;
	$p = HTML::TokeParser->new(\$content);
	while ($t = $p->get_tag('a')) {
        $href = $t->[1]{href};
        $href =~ s/.*www.senat.fr//;
	    if ($href =~ /\/(senateur|senfic)\//) {
		$i = 3;
		while ($i) {
				$i--;
				if (download_fiche($href) =~ /^ERROR: /) {
				    if (!$i) {
					print STDERR "Error downloading ".$href."\n";
				    }
				    sleep 1;
				}else{
					$i = 0;
				}
		}
	    }
	}
}

find_senateurs("https://web.archive.org/web/20230101000000/https://www.senat.fr/senateurs/senatl.html");
find_senateurs("https://web.archive.org/web/20230101000000/https://www.senat.fr/senateurs/news.html");
#find_senateurs("https://web.archive.org/web/20230101000000/https://www.senat.fr/senateurs/news_2010-2011.html");

#Enable below on first load
#find_senateurs("https://web.archive.org/web/20230101000000/https://www.senat.fr/senateurs/news_2009-2010.html");
#find_senateurs("https://web.archive.org/web/20230101000000/https://www.senat.fr/senateurs/news_2008-2009.html");
#find_senateurs("https://web.archive.org/web/20230101000000/https://www.senat.fr/senateurs/news_2007-2008.html");
#find_senateurs("https://web.archive.org/web/20230101000000/https://www.senat.fr/senateurs/news_2006-2007.html");
#find_senateurs("https://web.archive.org/web/20230101000000/https://www.senat.fr/senateurs/news_2005-2006.html");
#find_senateurs("https://web.archive.org/web/20230101000000/https://www.senat.fr/senateurs/news_2004-2005.html");
#find_senateurs("https://web.archive.org/web/20230101000000/https://www.senat.fr/anciens-senateurs-5eme-republique/senatl.html");
find_senateurs("https://web.archive.org/web/20230101000000/https://www.senat.fr/anciens-senateurs-5eme-republique/ump.html");
find_senateurs("https://web.archive.org/web/20230101000000/https://www.senat.fr/anciens-senateurs-5eme-republique/soc.html");
find_senateurs("https://web.archive.org/web/20230101000000/https://www.senat.fr/anciens-senateurs-5eme-republique/uc.html");
find_senateurs("https://web.archive.org/web/20230101000000/https://www.senat.fr/anciens-senateurs-5eme-republique/rdse.html");
find_senateurs("https://web.archive.org/web/20230101000000/https://www.senat.fr/anciens-senateurs-5eme-republique/crc.html");
find_senateurs("https://web.archive.org/web/20230101000000/https://www.senat.fr/anciens-senateurs-5eme-republique/ni.html");
find_senateurs("https://web.archive.org/web/20230101000000/https://www.senat.fr/anciens-senateurs-5eme-republique/lrem.html");
find_senateurs("https://web.archive.org/web/20230101000000/https://www.senat.fr/anciens-senateurs-5eme-republique/rtli.html");
