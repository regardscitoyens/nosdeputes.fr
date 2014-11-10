#!/usr/bin/perl
use WWW::Mechanize;
use HTML::TokeParser;
use URI::Escape;
use Encode;


$|=1;

$verbose = shift || 0;
%done = ();
sub download_fiche {
	$uri = shift;
	return if ($done{$uri});
	$done{$uri} = 1;
  	eval {
		$a->get("http://senat.fr".$uri);
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
	$a->get($url);
	$content = $a->content;
	$p = HTML::TokeParser->new(\$content);
	while ($t = $p->get_tag('a')) {
	    if ($t->[1]{href} =~ /\/(senateur|senfic)\//) {
		$i = 3;
		while ($i) {
				$i--;
				if (download_fiche($t->[1]{href}) =~ /^ERROR: /) {
					print STDERR "Error downloading ".$t->[1]{href}." ($i tries left)\n";
					sleep 1;
				}else{
					$i = 0;
				}
		}
	    }
	}
}

find_senateurs("http://www.senat.fr/senateurs/senatl.html");
find_senateurs("http://www.senat.fr/senateurs/news.html");
#find_senateurs("http://www.senat.fr/senateurs/news_2010-2011.html");

#Enable below on first load
#find_senateurs("http://www.senat.fr/senateurs/news_2009-2010.html");
#find_senateurs("http://www.senat.fr/senateurs/news_2008-2009.html");
#find_senateurs("http://www.senat.fr/senateurs/news_2007-2008.html");
#find_senateurs("http://www.senat.fr/senateurs/news_2006-2007.html");
#find_senateurs("http://www.senat.fr/senateurs/news_2005-2006.html");
#find_senateurs("http://www.senat.fr/senateurs/news_2004-2005.html");
#find_senateurs("http://www.senat.fr/anciens-senateurs-5eme-republique/senatl.html");
find_senateurs("http://www.senat.fr/anciens-senateurs-5eme-republique/ump.html");
find_senateurs("http://www.senat.fr/anciens-senateurs-5eme-republique/soc.html");
find_senateurs("http://www.senat.fr/anciens-senateurs-5eme-republique/uc.html");
find_senateurs("http://www.senat.fr/anciens-senateurs-5eme-republique/rdse.html");
find_senateurs("http://www.senat.fr/anciens-senateurs-5eme-republique/crc.html");
find_senateurs("http://www.senat.fr/anciens-senateurs-5eme-republique/ni.html");
