#!/usr/bin/perl

use WWW::Mechanize;
use HTML::TokeParser;
use Data::Dumper;

$dossier_url = shift;
if ($dossier_url =~ /\/([^\/]+)\.html$/) {
	$dossiersenat = $1;
}

if (!$dossier_url) {
	print "USAGE: perl parse_dossier.pl <url dossier senat> <include header>\n";
	print "\n";
	print "Transforme les dossiers du sénat en un CSV qui contient les url des textes aux différentes étapes\n";
	exit 1;
}

$a = WWW::Mechanize->new();
$a->get($dossier_url);
$content = $a->content;
$p = HTML::TokeParser->new(\$content);

if ($content =~ /timeline-1[^>]*<em>(\d{2})\/(\d{2})\/(\d{2})<\/em>/) {
	print "date : $1/$2/$3\n";
}

while ($t = $p->get_tag('div')) {
    if ($t->[1]{id} =~ /block-timeline/) {
	while ($t = $p->get_tag('em')) {
		$txt = $p->get_text('/em');
		if ($txt =~ /(\d{2})\/(\d{2})\/(\d{2})/) {
			$date = '20'.$3.$2.$1;
			last;
		}
        }
    }
    if ($t->[1]{id} =~ /^timeline-(\d+)/) {
	$id = $1;
	last;
    }
}

$ok = 1;
@lines = ();
while ($ok) {
    $t = $p->get_tag('em', 'img', 'a', 'div', 'h3');
    if($t->[0] eq 'em') {
	$etape = $p->get_text('/em');
	last if ($etape eq 'Loi');
	$etape =~ s/[^0-9a-z\. ]//;
	$etape =~ s/dfiniti/definiti/;
    }elsif($t->[0] eq 'img' && $t->[1]{src} =~ /picto_timeline_0([1234])_on.png/) {
	$img = $1;
	if ($img == 3) {
	    $stade = "commission";
	}elsif($img == 4) {
	    $stade = "hemicycle";
	}elsif ($img == 2) {
	    $chambre = "senat";
	    $stade = '';
	}elsif($img == 1) {
	    $chambre = "assemblee";
	    $stade = '';
	}
    }elsif($t->[0] eq 'a' && $t->[1]{href} !~ /^\#/) {
	if ($t->[1]{href} =~ /\/dossiers\/([^\.]+)\./) {
		if ($1 !~ /_scr$/) {
			$dossieran = $1;
		}
	}
      	if ($t->[1]{href} =~ /\/leg\/p/ || $p->get_text('/a') =~ /Texte/) {
	    $url = $t->[1]{href};
	    $url = "http://www.senat.fr".$url if ($url =~ /^\//) ;
            $idtext = '';
            if ($url =~ /assemblee-nationale/) {
		$chambre = 'assemblee' if ($stade eq 'hemicycle');
		if ($url =~ /[^0-9]0*([1-9][0-9]*)(-a\d)?\.asp$/) {
			$idtext = $1;
		}
            }elsif ($url =~ /senat.fr/) {
		$chambre = 'senat' if ($stade eq 'hemicycle');
		if ($url =~ /(\d{2})-(\d+)\.html$/) {
			$idtext='20'.$1.'20'.($1+1).'-'.$2;
		}
            }
	    $lines[$#lines+1] =  "$id;$etape;$chambre;$stade;$url;$idtext";
	    $url = '';
	}
	if ($t->[1]{href} =~ /^mailto:/) {
	    last;
	}
    }elsif($t->[0] eq 'div' && $t->[1]{id} =~  /^timeline-(\d+)/) {
	$id = $1;
    }elsif($t->[0] eq 'h3' && $t->[1]{class} =~ /title/) {
	if ($p->get_text('/h3') =~ /mixte paritaire/) {
	    $chambre = 'CMP';
            $etape = 'CMP';
	}
    }
}

$cpt = 0;
print "date ; an's dossier id ; senat's dossier id ; line id ; senat's step id ; stage ; chamber ; step ; bill url ; bill id\n" if (shift);
foreach $l (@lines) {
        $idline = sprintf("%02d", $cpt);
	print "$date;$dossieran;$dossiersenat;$idline;$l\n";
	$cpt++;
}

exit;
