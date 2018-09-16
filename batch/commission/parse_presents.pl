#!/usr/bin/perl

$file = $url = shift;
$special = shift;
$defaulthoraire = shift;
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
$string =~ s/‑/-/g;
$string =~ s/’/'/g;
$string =~ s/\r//g;
$string =~ s/(M\.\s*&nbsp;\s*)+/M. /g;
$string =~ s/\s*&(#160|nbsp);\s*/ /ig;
$string =~ s/&#278;/É/g;

if ($special && $url =~ /www2.assemblee/) {
  $commission = $special;
  $string =~ s/[\s\n]+/ /g;
  $string =~ s/[,\s]*<br[\/\s]*>[,\s]*/\n/g;
  $string =~ s/<\/?(p|h\d+|div)[^>]*>/\n<\1>/g;
  $string =~ s/((Excusé|Présent)[es\s]*:)\s*/\1\n/g;
  $string =~ s/<script>.*?<\/script>//g;
}

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

$heures{'zéro'} = '00';
$heures{'une'} = '01';
$heures{'deux'} = '02';
$heures{'trois'} = '03';
$heures{'quatre'} = '04';
$heures{'cinq'} = '05';
$heures{'six'} = '06';
$heures{'sept'} = '07';
$heures{'huit'} = '08';
$heures{'neuf'} = '09';
$heures{'dix'} = '10';
$heures{'onze'} = '11';
$heures{'douze'} = '12';
$heures{'treize'} = '13';
$heures{'quatorze'} = '14';
$heures{'quinze'} = '15';
$heures{'seize'} = '16';
$heures{'dix-sept'} = '17';
$heures{'dix-huit'} = '18';
$heures{'dix-neuf'} = '19';
$heures{'vingt'} = '20';
$heures{'vingt et une'} = '21';
$heures{'vingt-et-une'} = '21';
$heures{'vingt-deux'} = '22';
$heures{'vingt-trois'} = '23';
$heures{'vingt-cinq'} = '25';
$heures{'trente'} = '30';
$heures{'trente-cinq'} = '35';
$heures{'quarante'} = '40';
$heures{'quarante-cinq'} = '45';
$heures{'cinquante'} = '50';
$heures{'cinquante-cinq'} = '55';
$heures{''} = '00';

if ($special && $string =~ />(?:Décisions (?:de Questure )?de la )?[Rr]éunion (?:de Questure )?du (\w+\s+)?(\d+)[erme]*\s+([^\s\d]+)\s+(\d+)/) {
  $date = sprintf("%04d-%02d-%02d", $4, $mois{lc($3)}, $2);
  $heure = $defaulthoraire;
}
if ($string =~ />Réunion du (\w+\s+)?(\d+)[erme]*\s+([^\s\d]+)\s+(\d+)(?:\s+à\s+(\d+)\s*h(?:eure)?s?\s*(\d*))\.?</) {
  $tmpdate = sprintf("%04d-%02d-%02d", $4, $mois{lc($3)}, $2);
  $heure = sprintf("%02d:%02d", $5, $6 || '00');
}

if ($string =~ /réunion.*commission.*commence[^\.]+à\s+([^\.]+)\s+heures?\s*([^\.]*)\./i) {
  $heure = $heures{$1}.':'.$heures{$2};
}

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
    if ($#presents < 0) {
	print STDERR "$url: Pas de présent trouvé\n";
	return ;
    }
    $commission =~ s/"//g;
    if ($commission =~/^\s*Mission d'information\s*$/i && $commission_meta) {
        $commission = $commission_meta;
    }
    if (!$date) {
        $date = $tmpdate;
    }
    foreach $depute (@presents) {
	$depute =~ s/[\/<\|]//g;
	$depute =~ s/^\s*M+([mes]+\s+|\.\s*)//;
	$depute =~ s/\s+$//;
	$depute =~ s/^\s+//;
    if ($depute !~ /^Vice[ -]|Président|Questeur|Secrétaire|Présent|Excusé/i) {
      print '{"commission": "'.$commission.'","depute": "'.$depute.'","reunion":"'.$date.'","session":"'.$heure.'","source":"'.$source.'"}'."\n";
    }
    }
}

$string =~ s/\r//g;
$string =~ s/&#8217;/'/g;
$string =~ s/d\W+évaluation/d'évaluation/g;
$string =~ s/&#339;|œ+/oe/g;
$string =~ s/\|(\W+)\|/$1/g;
$string =~ s/ission d\W+information/ission d'information/gi;
$string =~ s/à l\W+aménagement /à l'aménagement /gi;
$majIntervenant = 0;
$body = 0;
$present = 0;
$string =~ s/<br>\n//gi;
$string =~ s/(<\/h\d+>)/\1\n/gi;

# Le cas de <ul> qui peut faire confondre une nomination à une intervention :
#on vire les paragraphes contenus et on didascalise
$string =~ s/<\/?ul>//gi;

#print $string; exit;

foreach $line (split /\n/, $string)
{
    if ($line =~ /<body[^>]*>/) {
	$body = 1;
    }
    if ($line =~ /<meta /) {
        if($line =~ /name="NOMCOMMISSION" CONTENT="([^"]+)"/i) {
            $commission_meta = $1;
        }
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
		if ($test !~ /(spéciale|enquête)$/i) {
			$commission = $test;
		}
	    }
	}
    }
    if ($line =~ /<h[1-9]+/i) {
        if (!$date && $line =~ /SOM(seance|date)|\"seance\"|h2/) {
            if ($line =~ /SOMdate|Lundi|Mardi|Mercredi|Jeudi|Vendredi|Samedi|Dimanche/i) {
              if ($line =~ /(\w+\s+)?(\d+)[erme]*\s+([^\s\d()!<>]+)\s+(\d\d+)/i) {
                $date = sprintf("%04d-%02d-%02d", $4, $mois{lc($3)}, $2);
              }
            }
        }elsif ($line =~ /SOMseance|"souligne_cra"/i) {
            if ($line =~ /(\d+)\s*(h(?:eures?)?)\s*(\d*)/i) {
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
    if (!$commission && $line =~ /^\s*<p[^>]*>\|([^<>]*(groupe|mission|délégation|office|comité)[^<>]*)\|<\/p>\s*$/i) {
        $commission = $1;
    }
    $origline = $line;
    if ($special && $line =~ /[pP]résidence de \|?M[.me]+ ([A-ZÉ][^|]*)\|?/) {
        push @presents, $1;
    }
    #print STDERR "TEST $special $present: $line\n";
    if ($origline =~ /Retour haut de page/) {
        $present = 0;
    }
    if ($present || ($special && $line =~ s/(<[^>]*>|\/)*(M[.me]+ .*) étai(en)?t présents?..*$/\2/g)) {
	$line =~ s/<[^>]+>//g;
	$line =~ s/&[^;]*;/ /g;
	$line =~ s/(M[.me]+ )\1/\1/g;
    if ($special) {
        next if ($line =~ /Secrétaire d'État/i);
        while ($line =~ s/(M[me.]+ [^,]+, )puis (M[me.]+ [^\/]+)/\1\2/) {}
        while ($line =~ s/M[me.]+ [^,]+, \/représentée? par (M[me.]+ [^\/]+)\//\1/) {}
        while ($line =~ s/^([^\/]*?)[, ]*\/représentant.*/\1/) {}
        while ($line =~ s/^([^\/]*?)[, ]*\/[^\/]*\//\1/) {}
    }
	$line =~ s/^\s*et\s+//gi;
	$line =~ s/\s+et\s+/, /gi;
	$line =~ s/\.$//;
	if ($line =~ s/\/?(Présents?|Assistai(en)?t également à la réunion|(E|É)tait également présent[es]*)\W+\s*// || ($newcomm && $line =~ /^\s*M+[\.mMes]+\s/) || $special) {
        $line =~ s/(^|\W+)M+[mes.]+\s+/\1/g;
        if ($line !~ /^([\/\s]*|\s*(le|la|chargée?) .*)$/) {
            push @presents, split /[.,] /, $line; #/
	    }
    }
    }
    $line =~ s/<\/?a[^>]*>//ig;
    # Cases of special orgs
    if ($special) {
      if ($origline =~ /Présent[es\s]*:/) {
        $present = 1;
      } elsif ($origline =~ /Excusé[es\s]*:/) {
        $present = 0;
      }
    } else {
    if ($line =~ /[>\|\/](Membres? présents?( ou excusés?)?|Présences? en réunion)[<\|\/]/ || $line =~ /[>\/\|]La séance est levée/ || $line =~ /^\s*Députés\s*$/) {
        $present = 1;
    } elsif ($line =~ /^\s*Sénateurs\s*$/) {
        $present = 0;
    }
    if ($origline =~ /^\s*<p[^>]*>\|([^<>]*(groupe|mission|délégation|office|comité)[^<>]*)\|<\/p>\s*$/i) {
        $newcomm = 1;
    } elsif ($line !~ /^\s*$/) {
        $newcomm = 0;
    }
    }
}
checkout();
