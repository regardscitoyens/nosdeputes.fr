#!/usr/bin/perl

$file = $url = shift;
#use HTML::TokeParser;
$url =~ s/^[^\/]+\///;
$url =~ s/_/\//g;
$source = $url;

if ($url =~ /\/(\d+)-(\d+)\//) {
    $session = '20'.$1.'20'.$2;
}

open(FILE, $file) ;
@string = <FILE>;
$string = "@string";
close FILE;

$string =~ s/<\/?b>/|/g;
$string =~ s/<\/?i>/\//g;
$string =~ s/\r//g;

$mois{'janvier'} = '01';
$mois{'février'} = '02';
$mois{'mars'} = '03';
$mois{'avril'} = '04';
$mois{'mai'} = '05';
$mois{'juin'} = '06';
$mois{'juillet'} = '07';
$mois{'août'} = '08';
$mois{'septembre'} = '09';
$mois{'octobre'} = '10';
$mois{'novembre'} = '11';
$mois{'décembre'} = '12';


#utf8::decode($string);
#
#$p = HTML::TokeParser->new(\$string);
#
#while ($t = $p->get_tag('p', 'h1', 'h5')) {
#    print "--".$p->get_text('/'.$t->[0])."\n";
#}
#
#exit;
$cpt = 0;
sub checkout {
    if ($#presents <= 0) {
	print STDERR "$source: Pas de présent trouvé\n";
	return ;
    }
    $commission =~ s/"//g;
    foreach $depute (@presents) {
	$depute =~ s/[\/<\|]//g;
	$depute =~ s/^\s*M[me\.]+\s+//;
	$depute =~ s/\s+$//;
	print '{"reunion": "'.$date.'", "session": "'.$heure.'", "commission": "'.$commission.'", "depute": "'.$depute.'", "source": "'.$source.'"}'."\n";
    }
}

$string =~ s/\r//g;
$string =~ s/&nbsp;/ /g;
$string =~ s/&#8217;/'/g;
$string =~ s/&#339;|œ+/oe/g;
$string =~ s/\|(\W+)\|/$1/g;
$string =~ s/ission d\W+information/ission d'information/gi;
$string =~ s/à l\W+aménagement /à l'aménagement /gi;
$majIntervenant = 0;
$body = 0;
$present = 0;
$string =~ s/<br>\n//gi;

# Le cas de <ul> qui peut faire confondre une nomination à une intervention : 
#on vire les paragraphes contenus et on didascalise


$string =~ s/<\/?ul>//gi;

#print $string; exit;

foreach $line (split /\n/, $string)
{
    if ($line =~ /<body[^>]*>/) {
	$body = 1;
    }
    next unless ($body);
    if ($line =~ /\<[a]/i) {
	if ($line =~ /<a name=["']([^"']+)["']/) {
	    $source = $url."#$1";
	}elsif($line =~/class="menu"/ && $line =~ /<a[^>]+>([^<]+)<?/) {
	    $test = $1;
	    if (!$commission && $test =~ /Commission|mission/) {
		$test =~ s/ Les comptes rendus de la //;
		$test =~ s/^ +//;
		if ($test !~ /spéciale$/i) {
			$commission = $test;
		}
	    }
	}
    }
    if ($line =~ /<h[1-9]+/i) {
	if (!$date && $line =~ /SOMdate|\"seance\"|h2/) {
	    if ($line =~ /SOMdate|Lundi|Mardi|Mercredi|Jeudi|Vendredi|Samedi|Dimanche/i) {
	      if ($line =~ /\w+\s+(\d+)[erme]*\s+([^\s\d]+)\s+(\d+)/i) {
		$date = sprintf("%04d-%02d-%02d", $3, $mois{lc($2)}, $1);
	      }
	    }
	}elsif ($line =~ /SOMseance|"souligne_cra"/i) {
	    if ($line =~ /(\d+)\s*(h|heures?)\s*(\d*)/i) {
		$heure = sprintf("%02d:%02d", $1, $3 || "00");
	    }
	}elsif(!$commission && $line =~ /groupe|commission|mission|délégation|office|comité/i) {
	    if ($line =~ /[\>\|]\s*((Groupe|Com|Miss|Délé|Offic)[^\>\|]+)[\<\|]/) {
		$commission = $1;
		$commission =~ s/\s*$//;
	    }
	}elsif($line =~ /SOMnumcr/i) {
	    if ($line =~ /\s0*(\d+)/ && $1 > 1) {
		$cpt = $1*1000000;
	    }
	}
    }
    if ($present) {
	$line =~ s/<[^>]+>//gi;
	$line =~ s/&[^;]*;/ /gi;
	if ($line =~ s/\/?(Présents|Assistai(en)?t également à la réunion)\W+//) {
	    push @presents, split /, /, $line; #/
	}
    }
    if ($line =~ /[>\|\/](Membres? présents? ou excusés?|Présences? en réunion)[<\|\/]/ || $line =~ /[>\/\|]La séance est levée/) {
	$present = 1;
    }
}
checkout();
