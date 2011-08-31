#!/usr/bin/perl

use URI::Escape;
require "../common/common.pm";

$file = shift;
$url_source = uri_unescape($file);
$url_source =~ s/.*html.*\/http/http/;


open FILE, $file;
@lignes = <FILE>;
close FILE;
$content = "@lignes";
$content =~ s/\n//g;
$content =~ s/(<td[^>]*>)(\s*<\/?(a|strong|p|em)[^>]*>)+/$1/gi;
$content =~ s/<\/?(a|strong|p|em)[^>]*>\s*<\/td>/<\/td>/gi;

$content =~ s/<\/(p|h[1234]|ul|div)>/<\/$1>\n/gi;

$timestamp = 0;
$nb_seance = 0;
sub print_inter {
	if ($intervention && $intervention ne '<p></p>') {
		if ($intervention =~ /(projet de loi|texte)( n[^<]+)/) {
			$doc = $2;
			$doc =~ s/&[^;]+;//g;
			$numeros_lois = '';
			while ($doc =~ / n\s*(\d+) ?(\(\d+\-\d+\))/g) {
				$numeros_loi .= "$1-$2,";
			}
			$numeros_loi =~ s/[^0-9\-\,]//g;
			chop($numeros_loi);
		}
		if ($intervention =~ /amendement( n[^<]+)/) {
			$doc = $1;
			$doc =~ s/&[^;]+;//g;
                        if ($doc =~ / n\s*([COM\-\d]+)/) {
				$amendements = $1;
			}
		}
		$timestamp += 20;
		print '{"commission": "'.$commission.'", "contexte": "'.$context.'", "intervention": "'.$intervention.'", "timestamp": "'.$timestamp.'", "date": "'.$date.'", "source": "'.$url_source.$source.'", "heure":"'.$heure.'", "intervenant": "'.$intervenant.'", "fonction": "'.$fonction.'", "intervenant_url": "'.$url_intervenant.'", "session":"'.$session.'"';
        	print ', "numeros_loi":"'.$numeros_loi.'"' if ($numeros_loi);
	        print ', "amendements":"'.$amendements.'"' if ($amendements);
		print "}\n";
	}
	$intervenant = '';
	$fonction = '';
	$url_intervenant = '';
	$intervention = '';
	$amendements = '';
}

sub setfonction {
	shift;
	if (/audition de (M[^<]+)/) {
		$a = $1;
		while ($a =~ /(M[me\.]* [^\,\.]+), ([^\,\.]+)/g) {
			$fonctions{$1} = $2;
		}
	}
}

$begin = 0;
foreach (split /\n/, $content) {
	$begin = 1 if (/name="toc1"/);
	$commission = $1 if (/TITLE>(Commission [^:]*)&nbsp;:/);
	next if (!$begin);
#	print ;	print "\n";
	if (/<h2>([^<]+)<\/h2>/) {
		@date = datize($1);
		print_inter();
		$date = join '-', @date;
		$session = sessionize(@date);
		$numeros_loi = '';
		$nb_seance = 0;
	}
	if (/<h3>([^<]+)<\/h3>/) {
		$titre = $1;
		print_inter();
		$context = $titre;
		setfonction($titre);
		$context =~ s/ - / > /;
		$intervention = '<p>'.$titre.'</p>';
		$nb_seance++;
		$heure = ($nb_seance == 1) ? '1ere' : $nb_seance.'ieme';
		$heure .= ' séance';
		$timestamp = 0;
		$numeros_loi = '';
		print_inter();
	}
	$source = "#$1" if (/name="([^"]+)"/);

	if (/<p[^>]*>(.*)<\/p>/i) {
		$inter = $1;
		$inter =~ s/<a[^>]*><\/a>//ig;
print "inter: $inter\n";
		if ($inter =~ /^<(u|strong|em)>(.*)<\/(u|strong|em)>$/i) {
			$inter = $2;
			print_inter();
	                $inter =~ s/<[^>]+>//g;
			setfonction($inter);
			$intervention = '<p>'.$inter.'</p>';
			next;
		}
		if ($inter =~ /<(a|strong)[^>]*>(.*)<\/(a|strong)>/i) {
			$tmpintervenant = $2;
			$tmpintervenant =~ s/<[^>]*>//g;
			print_inter() if ($tmpintervenant ne $intervenant);
			$intervenant = $tmpintervenant;
			if ($intervenant =~ s/, (.*)//g) {
				$fonction = $1;
				$fonction =~ s/\W+$//;
				$fonctions{$intervenant} = $fonction;
			}else{
				$fonction = $fonctions{$intervenant};
			}
			$url_intervenant = $1 if ($inter =~ /href="([^"]+senfic\/[^"]+)"/i);
		}
		$inter =~ s/<[^>]+>//g;
		$inter =~ s/^\W*$intervenant\W*($fonction\W*|)//;
		$intervention .= '<p>'.$inter.'</p>' if ($inter =~ /[a-z]/i);
	}
#	print "$date $titre $source\n";
}
