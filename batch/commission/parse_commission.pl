#!/usr/bin/perl

$file = $url = shift;
#use HTML::TokeParser;
$url =~ s/^[^\/]+\///;
$url =~ s/_/\//g;
$url =~ s/commissions\/elargies/commissions_elargies/;
$source = $url;

if ($url =~ /\/(\d+)-(\d+)\//) {
  $session = '20'.$1.'20'.$2;
} elsif ($url =~ /\/plf(\d+)\//) {
  $annee = $1-1;
  $session = $annee.$1;
}

open(FILE, $file) ;
@string = <FILE>;
$string = "@string";
close FILE;

$string =~ s/<\/?b>/|/g;
$string =~ s/<\/?i>/\//g;
$string =~ s/\r//g;

if ($url =~ /\/plf(\d+)\//) {
  $string2 = $string;
  $string2 =~ s/\n//g;
  $string2 =~ s/\<br\/?\>//ig;
  $string2 =~ s/&nbsp;/ /ig;
  $string2 =~ s/&#8217;/'/ig;
  $string2 =~ s/^.*Commission élargie//;
  $string2 =~ s/\(Application de l'article 120 du Règlement.*$//;
  $string2 =~ s/\<\/?[a-z0-9\s\-_="']+\>//ig;
  $string2 =~ s/\s+/ /g;
  $string2 =~ s/^\s+//;
  $string2 =~ s/[^a-z]+$//;
  $string2 =~ s/ Comm/, Comm/g;
  $commission = "Commission élargie : ".$string2;
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

$heure{'neuf'} = '09';
$heure{'dix'} = '10';
$heure{'onze'} = '11';
$heure{'douze'} = '12';
$heure{'treize'} = '13';
$heure{'quatorze'} = '14';
$heure{'quinze'} = '15';
$heure{'seize'} = '16';
$heure{'dix-sept'} = '17';
$heure{'dix-huit'} = '18';
$heure{'dix-neuf'} = '19';
$heure{'vingt'} = '20';
$heure{'vingt et une'} = '21';
$heure{'vingt-deux'} = '22';
$heure{'quarante'} = '45';
$heure{'quarante-cinq'} = '45';
$heure{'trente'} = '30';
$heure{'trente-cinq'} = '35';
$heure{'quinze'} = '15';
$heure{'zéro'} = '00';
$heure{'cinq'} = '00';
$heure{''} = '00';

if ($string =~ /réunion.*commission.*commence[^\.]+à ([^\.]+)( |&nbsp;)heures?\s*([^\.]*)\./i) {
    $heure = $heure{$1}.':'.$heure{$3};
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
    $cpt+=10;
    $out =  '{"commission": "'.$commission.'", "intervention": "'.$intervention.'", "timestamp": "'.$cpt.'", "date": "'.$date.'", "source": "'.$source.'", "heure":"'.$heure.'", "session": "'.$session.'", ';
    if ($intervenant) {
	if ($intervenant =~ s/ et M[mes\.]* (.*)//) {
	    print $out.'"intervenant": "'.$1."\"}\n";
	}
	print $out.'"intervenant": "'.$intervenant.'", "fonction": "'.$inter2fonction{$intervenant}."\"}\n";
    }elsif($intervention) {
	print $out.'"intervenant":"'."\"}\n";
    }else {
	return ;
    }
    $commentaire = "";
    $intervenant = "";
    $intervention = "";
}

sub setFonction {
    my $fonction = shift;
    my $intervenant = setIntervenant(shift);
    $fonction =~ s/\W+$//;
    $fonction =~ s/<[^>]+>\s*//g;
    $fonction =~ s/<[^>]*$//;
    my $kfonction = lc($fonction);
    $kfonction =~ s/[^a-zéàè]+/ /gi;
    $fonction2inter{$kfonction} = $intervenant;
#    print "$fonction ($kfonction)  => $intervenant-".$inter2fonction{$intervenant}."\n";
    if (!$inter2fonction{$intervenant}) {
	$inter2fonction{$intervenant} = $fonction;
    }
}

sub setIntervenant {
    my $intervenant = shift;
#    print "$intervenant\n";
    $intervenant =~ s/^(M(\.|me))(\S)/$1 $2/;
    $intervenant =~ s/[\|\/\.]//g;
    $intervenant =~ s/\s*\&\#8211\;\s*$//;
    $intervenant =~ s/\s*[\.\:]\s*$//;
    $intervenant =~ s/Madame/Mme/;
    $intervenant =~ s/Monsieur/M./;
    $intervenant =~ s/et M\. /et M /;
    $intervenant =~ s/^M[\.mes]*\s//i;
    $intervenant =~ s/\s*\..*$//;
    $intervenant =~ s/L([ea])\s/l$1 /i;
    $intervenant =~ s/\s+\(/, /g;
    $intervenant =~ s/\)//g;
    $intervenant =~ s/[\.\,\s]+$//;
    $intervenant =~ s/^\s+//;
    $intervenant =~ s/É+/é/gi;
    $intervenant =~ s/\&\#8217\;/'/g;
    if ($intervenant =~ s/\, (.*)//) {
	setFonction($1, $intervenant);
    }
    if ($intervenant =~ /^[a-z]/) {
	$intervenant =~ s/^l[ea]\s+//i;
	if ($intervenant =~ /([pP]résidente?|[rR]apporteur[a-zé\s]+)\s([A-Zé].*)/) { #\s([A-Z].*)/i) {
	    setFonction($1, $2);
	    return $2;
	}
	$conv = $fonction2inter{$intervenant};
#	print "conv: '$conv' '$intervenant'\n";
	if ($conv) {
	    $intervenant = $conv;
	}else {
	    $test = lc($intervenant);
	    $test =~ s/[^a-z]+/ /gi;
	    foreach $fonction (keys %fonction2inter) {
		if ($fonction =~ /$test/) {
		    $inter = $fonction2inter{$fonction};
		    last;
		}
	    }
	    if (!$inter) {
		foreach $fonction (keys %fonction2inter) {
		    if ($test =~ /$fonction/) {
			$inter = $fonction2inter{$fonction};
			last;
		    }
		}
	    }
	    if ($inter) {
		$intervenant = $inter;
	    }
	}
    }
    return $intervenant;
}

sub rapporteur
{
    #Si le commentaire contient peu nous aider à identifier le rapport, on tente
    if ($line =~ /rapport/i) {
	if ($line =~ /M[me\.]+\s([^,]+), (rapporteur[^\)\,\.\;]*)/i) {
	    setFonction($2, $1);
	}elsif ($line =~ /rapport de \|?M[me\.]+\s([^,\.\;\|]+)[\,\.\;\|]/i) {
	    setFonction('rapporteur', $1);
	}
    }
}

$string =~ s/\r//g;
$string =~ s/&nbsp;/ /g;
$string =~ s/&#8217;/'/g;
$string =~ s/d\W+évaluation/d'évaluation/g;
$string =~ s/&#339;|œ+/oe/g;
$string =~ s/\|(\W+)\|/$1/g;
$string =~ s/ission d\W+information/ission d'information/gi;
$string =~ s/à l\W+aménagement /à l'aménagement /gi;
$majIntervenant = 0;
$body = 0;

$string =~ s/<br>\n//gi;

# Le cas de <ul> qui peut faire confondre une nomination à une intervention : 
#on vire les paragraphes contenus et on didascalise


$string =~ s/<\/?ul>//gi;

#print $string; exit;

foreach $line (split /\n/, $string)
{
#print "TEST: ".$line."\n";
    if ($line =~ /<h[1-9]+/i || $line =~ /"presidence"/) {
      if ($line =~ /pr..?sidence de (M[^<\,]+)[<,]/i && $line !~ /sarkozy/i) {
        $prez = $1;
#       print "Présidence de $prez\n";
        if ($prez =~ /^Mm/) {
          setFonction('présidente', $prez);
        }else {
          setFonction('président', $prez);
        }
      }
    }
    if ($line =~ /<body[^>]*>/) {
	$body = 1;
    }
    next unless ($body);
    if ($line =~ /fpfp/) {
	checkout();
	next;
    }
    if ($line =~ /\<[a]/i) {
	if ($line =~ /<a name=["']([^"']+)["']/) {
	    $source = $url."#$1";
	}elsif($line =~ /class="menu"/ && $line =~ /<a[^>]+>([^<]+)<?/) {
	    $test = $1;
	    if (!$commission && $test =~ /Commission|mission/) {
		$test =~ s/\s*Les comptes rendus de la //;
		$test =~ s/^ +//;
		if ($test !~ /spéciale$/i) {
			$commission = $test;
		}
	    }
	}
    }
    if ($line =~ /<h[1-9]+/i) {
        rapporteur();
#       print "$line\n";
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
    if ($line =~ /\<[p]/i || ($line =~ /\<h[1-9]+ class="titre\d+/i && $line !~ /Commission/)) {
	$found = 0;
	$line =~ s/\s*\<\/?[^\>]+\>//g;
	$line =~ s/^\s+//;
	last if ($line =~ /^\|annexe/i);
	next if ($line !~ /\w/);

	#si italique ou tout gras => commentaire
	if ($line =~ /^\|.*\|\s*$/ || $line =~ /^\/.*\/\s*$/) {
	    checkout() if ($intervenant);	    
	    rapporteur();
	    $found = 1;
	}elsif ($line =~ s/^\|(M[^\|\:]+)[\|\:](\/[^\/]+\/)?// ) {
	    checkout();
	    $majIntervenant = 1;
            $interv1 = $1;
	    $extrainterv = $2;
	    if ($extrainterv =~ s/(\/A \w+i\/)//) {
	      $line = $1.$line;
            }
	    $intervenant = setIntervenant($interv1.$extrainterv);
	    $found = 1;
	}elsif ($line =~ s/^\|([^\|,]+)\s*,\s*([^\|]+)\|// ) {
	    checkout();
            $found = $majIntervenant = 1;
	    setFonction($2, $1);
	    $intervenant = setIntervenant($1);
	}elsif ($line =~ s/^[Llea\s]*\|[Llea\s]*([pP]résidente?) (([A-ZÉ][^\.: \|]+ ?)+)[\.: \|]*//) {
		$f = $1;
		$i = $2;
		$found = $majIntervenant = 1;
                checkout();
                setFonction($f, $i);
		$intervenant = setIntervenant($i);
	}elsif ($line =~ s/^[Llea\s]*\|[Llea\s]*([pP]résidente?|[rR]apporteure?)[\.: \|]*//) {
		$tmpfonction = lc($1);
		$tmpintervenant = $fonction2inter{$tmpfonction};
		if ($tmpintervenant) {
	                checkout();
			$intervenant = $tmpintervenant;
			$found = $majIntervenant = 1;
		}
	}
	$line =~ s/^\s+//;
	$line =~ s/[\|\/]//g;
	$line =~ s/^[\.\:]\s*//;
	if (!$majIntervenant && !$found) {
	    if     ($line =~ s/^\s*(M[mes\.]+\s[^\.:]+)[\.:]//) {
		checkout();
		$intervenant = setIntervenant($1);		
	    }elsif ($line =~ s/^\s*(M[mes\.]+\s[A-Z][^\s\,]+\s*([A-Z][^\s\,]+\s*|de\s*){2,})// ) {
		checkout();
		$intervenant = setIntervenant($1);
	    }elsif($line =~ s/^\s*[Ll][ea] ([pP]résidente?) (([A-ZÉ][^\.: \|]+ ?)+)[\.: \|]*//) {
                setFonction($1, $2);
                checkout();
                $intervenant = setIntervenant($2);
	    }
	}
	$intervention .= "<p>$line</p>";
	if ($line =~ /séance est levée/i) {
	    last;
	}
    }
}
checkout();
