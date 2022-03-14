#!/usr/bin/perl

$file = shift;
$url = shift;
$special = shift;
$defaulthoraire = shift;
use HTML::TokeParser;
use Time::Piece;
$today = Time::Piece->new();

$source = $url;
$raw = 0;
if ($file =~ /CRCANR.*\.html/) {
  $raw = 1;
}

open(FILE, $file);
@string = <FILE>;
$string = "@string";
close FILE;

if ($string =~ /href="\/dyn\/opendata\/([^"]+\.html)"/) {
  open(FILE, "raw/$1");
  @string = <FILE>;
  $string = "@string";
  close FILE;
  $raw = 1;
}

$string =~ s/<br>\n//gi;
$string =~ s/(<p[^>]*>)\s*\n\s*/\1/gi;
$string =~ s/<span [^>]*font-weight:bold; font-style:italic[^>]*>([^<]*)<\/span>/<b><i>\1<\/i><\/b>/gi;
$string =~ s/<span [^>]*font-weight:bold[^>]*>([^<]*)<\/span>/<b>\1<\/b>/gi;
$string =~ s/<span [^>]*font-weight:italic[^>]*>([^<]*)<\/span>/<i>\1<\/i>/gi;
$string =~ s/<span [^>]*>([^<]*)<\/span>/\1/gi;
$string =~ s/\s*<\/b>\s*<br[ \/]*>\s*<b>\s*/ /g;
$string =~ s/<a id="[^"]*">(.*?)<\/a>/\1/g;
$string =~ s/(<\/h\d+>)/\1\n/gi;

$string =~ s/<\/?b>/|/g;
$string =~ s/<\/?i>/\//g;
$string =~ s/‑/-/g;
$string =~ s/’/'/g;
$string =~ s/\r//g;
$string =~ s/(M\.\s*&nbsp;\s*)+/M. /g;
$string =~ s/\s*&(#160|nbsp);\s*/ /ig;
$string =~ s/&#278;/É/g;
$string =~ s/&#xa0;/ /g;

if ($special && $url =~ /www2.assemblee/) {
  $commission = $special;
  $string =~ s/[\s\n]+/ /g;
  $string =~ s/Réunion (lun|mar|mercre|jeu|vendre)di du /Réunion du \1di /g;
  $string =~ s/[,\s]*<br[\/\s]*>[,\s]*/\n/g;
  $string =~ s/<\/?(p|h\d+|div)[^>]*>/\n<\1>/g;
  $string =~ s/((Excusé|Présent)[es\s]*:)\s*/\1\n/g;
  $string =~ s/<script>.*?<\/script>//g;
}

if ($raw) {
  $string =~ s/(<p class="assnat[A-Z]+")([^>]*>)[\n\s\t]*(.*)[\n\s\t]*<\/p>[\n\s\t]*\1[^>]*>[\n\s\t]*(.*)[\n\s\t]*<\/p>/\1\2\3 \4<\/p>/g;
  $string =~ s/(<p class="assnat[^>]*>)\n/\1/gi;
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

if ($raw == 1) {
  $p = HTML::TokeParser->new(\$string);
  while ($t = $p->get_tag('p')) {
    $txt = $p->get_trimmed_text('/p');
    if (($t->[1]{class} eq "assnatNOMCOMMISSION" || $t->[1]{class} eq "assnatStylenomcommissionAvant0ptAprs20pt") && !$commission) {
      $commission = $txt;
    } elsif ($t->[1]{class} eq "assnatCRDATE" || $t->[1]{class} eq "assnatCRHEURE") {
      if ($txt =~ /(?:(?:Lun|Mar|Mercre|Jeu|Vendre)di|Dimanche)\s+(\d+)[erme]*\s+([^\s\d]+)\s+(20\d+)/i && !$date) {
        $date = sprintf("%04d-%02d-%02d", $3, $mois{lc($2)}, $1);
      } elsif ($txt =~ /(?:Réunion|Séance)\s+de\s*(\d+)\s*h(?:eure)?s?\s*(\d*)/i && !$heure) {
        $heure = sprintf("%02d:%02d", $1, $2 || '00');
      }
    }
  }
  if (!$date && !$commission && !$heure) {
    $p = HTML::TokeParser->new(\$string);
    while ($t = $p->get_tag('div')) {
      if ($t->[1]{class} eq "assnatSection1") {
        $txt = $p->get_text('/div');
        foreach $line (split /\n/, $txt) {
          if (!$commission && $line =~ /^\s*(Groupe|Commission|Mission|Délégation|Office)(.*)\s*$/) {
            $commission = "$1$2";
          } elsif ($line =~ /^\s*(?:(?:Lun|Mar|Mercre|Jeu|Vendre)di|Dimanche)\s+(\d+)[erme]*\s+([^\s\d]+)\s+(20\d+)/i && !$date) {
            $date = sprintf("%04d-%02d-%02d", $3, $mois{lc($2)}, $1);
          } elsif ($line =~ /^\s*(?:Réunion|Séance)\s+de\s*(\d+)\s*h(?:eure)?s?\s*(\d*)/i && !$heure) {
            $heure = sprintf("%02d:%02d", $1, $2 || '00');
          }
        }
      }
    }
  }
}

if ($special && $string =~ />(?:Décisions (?:de Questure )?de la )?(?:Décisions|[Rr]éunion) (?:de Questure )?du (\w+\s+)?(\d+)[erme]*\s+([^\s\d]+)\s+(\d+)?/) {
  if ($4 eq "") {
    $year = $today->year;
  } else {
    $year = $4;
  }
  $date = sprintf("%04d-%02d-%02d", $year, $mois{lc($3)}, $2);
  $heure = $defaulthoraire;
}
if ($string =~ />(?:Réunion|Séance) du (\w+\s+)?(\d+)[erme]*\s+([^\s\d]+)\s+(\d+)(?:\s+à\s+(\d+)\s*h(?:eure)?s?\s*(\d*))\.?</) {
  $tmpdate = sprintf("%04d-%02d-%02d", $4, $mois{lc($3)}, $2);
  $heure = sprintf("%02d:%02d", $5, $6 || '00');
}

if (!$tmpdate && $string =~ /(?:>|\n\s*)\|?(?:Lun|Mar|Mercre|Jeu|Vendre|Same)di(?:\s+|<br[ \/]*>)+(\d+)[erme]*\s+([^\s\d]+)\s+(\d{4})\|?(<|\n)/i) {
  $tmpdate = sprintf("%04d-%02d-%02d", $3, $mois{lc($2)}, $1);
}

if (!$heure && $string =~ />Séance de (\d+)\s*h(?:eures?)?\s*(\d*)\s*(<|\n)/) {
  $heure = sprintf("%02d:%02d", $1, $2 || '00');
}

if (!$heure && $string =~ /(?:réunion.*commission.*commence|séance est ouverte)[^<\.]+à\s+([^<\.]+)\s+heures?\s*([^<\.]*)\./i) {
  $heure = ($heures{$1} || $1).':'.($heures{$2} || $2);
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
    $commission =~ s/"//g;
    if ($commission =~/^\s*Mission d'information\s*$/i && $commission_meta) {
        $commission = $commission_meta;
    }
    $commission =~ s/^Commission des affaires sociales (Mission)/\1/i;
    if (!$date) {
        $date = $tmpdate;
    }
    if ($commission =~ /^\s*$/) {
        print STDERR "ERROR: $url : Pas de nom de commission\n";
        return ;
    }
    if ($#presents < 0) {
        print STDERR "$url : Pas de présent trouvé\n";
        return ;
    }
    foreach $depute (@presents) {
	$depute =~ s/[\/<\|]//g;
	$depute =~ s/^\s*M+([mes]+\s+|\.\s*|onsieur le |adame la |$)//;
	$depute =~ s/[,\s]+$//;
	$depute =~ s/^\s+//;
    $depute =~ s/Assistaient également à la réunion//;
	$depute =~ s/Monica Michel$/Monica Michel-Brassart/;
	$depute =~ s/Audrey Dufeu.?Schubert/Audrey Dufeu/;
	$depute =~ s/Florence Lasserre.?David/Florence Lasserre/;
	$depute =~ s/Charlotte Lecocq/Charlotte Parmentier-Lecocq/;
	$depute =~ s/Claire Picolât/Claire Pitollat/;
    if ($depute !~ /^(Vice[ -]|Président|Questeur|Parlementaire|Député|Membres?( français)? du parlement|Sénat|Secrétaire|Présent|Excusé|:)/i && $depute !~ /^\s*$/ && $depute !~ /\((député|sénateur|membre du parlement)[^)]*\)$/i) {
      print '{"commission": "'.$commission.'", "depute": "'.$depute.'", "reunion": "'.$date.'", "session": "'.$heure.'", "source": "'.$source.'", "source_file": "'.$file.'"}'."\n";
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
		if ($test !~ /(spéciale|enquête|sp|enqu)$/i) {
			$commission = $test;
		}
	    }
	}
    }
    if ($line =~ /<h[1-9]+/i) {
        if ($line =~ /SOMseance|"souligne_cra"/i) {
            if ($line =~ /(\d+)\s*(h(?:eures?)?)\s*(\d*)/i) {
                $heure = sprintf("%02d:%02d", $1, $3 || "00");
            }
        }
        if (!$date && $line =~ /SOM(seance|date)|\"seance\"|h2/) {
            if ($line =~ /SOMdate|Lundi|Mardi|Mercredi|Jeudi|Vendredi|Samedi|Dimanche/i) {
              if ($line =~ /(\w+\s+)?(\d+)[erme]*\s+([^\s\d()!<>]+)\s+(\d\d+)/i) {
                $date = sprintf("%04d-%02d-%02d", $4, $mois{lc($3)}, $2);
              }
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
    } elsif ($line =~ /assnatCRHEURE.*>\s*(\d+)\s*(h(?:eures?)?)\s*(\d*)/i) {
        $heure = sprintf("%02d:%02d", $1, $3 || "00");
    }
    if (!$commission && $line =~ /^\s*<p[^>]*>\|([^<>]*(groupe|(com)?mission|délégation|office|comité)[^<>]*)\|/i) {
        $commission = $1;
    }
    $origline = $line;
    if ($special && $line =~ /[pP]résidence de \|?M[.me]+ ([A-ZÉ][^|]*)\|?/) {
        $present = 1;
        push @presents, $1;
        next;
    }
    #print STDERR "TEST $special $present: $line\n";
    if ($origline =~ /Retour haut de page/) {
        $present = 0;
    }
	if (!$special && $line =~ /\/?(Présents?|Assistai(en)?t également à la réunion|(E|É)tait également présent[es]*)[^\wé]+\s*/ && $line !~ /Présents? sur /) {
        $present = 1;
    }
    if ($present || ($special && $line =~ s/(<[^>]*>|\/)*(M[.me]+ .*) (participai(en)?t à la réunion|étai(en)?t présents?)..*$/\2/g)) {
	$line =~ s/<[^>]+>//g;
	$line =~ s/&[^;]*;/ /g;
	$line =~ s/(M[.me]+ )\1/\1/g;
    if ($special) {
        if ($line =~ /LCP/) {
            $present = 0;
            next;
        }
        next if ($line =~ /Secrétaire d'État|Ministre|VP absents/i);
        while ($line =~ s/(M[me.]+ [^,]+, )puis (M[me.]+ [^\/]+)/\1\2/) {}
        while ($line =~ s/M[me.]+ [^,]+(?:, |\/)+représentée? par (M[me.]+ [^\/]+)\//\1/) {}
        while ($line =~ s/^([^\/]*?)[, ]*\/représentant.*/\1/) {}
        while ($line =~ s/^([^\/]*?)[, ]*\/[^\/]*\//\1/) {}
        $line =~ s/([A-Z]{3,}) (Rapporteur|(Vice-)?Président|).*$/\1/;
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
      if ($origline =~ /Présent[es]*( ou excusés?)?[\s\/]*:/) {
        $present = 1;
      } elsif ($origline =~ /Excusé[es\s]*[:\/]/) {
        $present = 0;
      } elsif ($origline =~ /Assistai[en]*t également\s*[:\/]/) {
        $present = 1;
      }
    } else {
    if ($line =~ /[>\|\/](Membres? (de la commission[\w\s]*?)?présents?( ou excusés?)?|Présences? en réunion)[<\|\/]/ || $line =~ /[>\/\|]La séance est levée/ || $line =~ /^\s*Députés\s*$/) {
        $present = 1;
    } elsif ($line =~ /^\s*[sS]énateurs\s*(:|$)/) {
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
