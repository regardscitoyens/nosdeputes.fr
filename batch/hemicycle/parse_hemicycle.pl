#!/usr/bin/perl

$file = $url = shift;
#use HTML::TokeParser;
$url =~ s/^[^\/]+\///;
$url =~ s/_/\//g;
$source = $url;

if ($url =~ /\/(\d{4})\-(\d{4})[\/\-]/) {
    $session = $1.$2;
}

open(FILE, $file) ;
@string = <FILE>;
$string = "@string";
close FILE;

#Si italique dans gras, on vire (pb fonction)
if ($string =~ /M[me\.]+[ \&][^<]+<\/a>\.[^<]*<\/b>[^<]*<i>([^<]+)</ && $1 =~ /rapporteur|president/i) {
	$string =~ s/(M[me\.]+[ \&][^<]+<\/a>)\.[^<]*<\/b>[^<]*<i>/$1,<\/b><i>/g;
}
$string =~ s/([^\.])\s*<\/b>\s*<i>([^<]+)<\/i>/$1 $2<\/b>/g;

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
$heure{''} = '00';

if ($string =~ /ouverte[^\.]+à ([^\.]+) heures?\s*([^\.]*)\./) {
    $heure = $heure{$1}.':'.$heure{$2};
}

$string =~ s/<\/?sup>//g;
#Recherche des numéros de  de loi
while($string =~ /ordre du jour ([^<]+ loi [^<]+)\(n\D+(\d+[^\)]+)\)/ig) {
    $no = $2;
    if ($no) {
	$titre = lc $1;
	$titre =~ s/[^<]+ loi //;
	$ploi{$titre} = $no
    }	
}

sub getProjetLoi {
    $titre_cleaned = $titre = lc shift;
    return unless ($titre);
    return $ploi{$titre} if (defined($ploi{$titre}));
    $titre_cleaned =~ s/[^a-z]+/ /g;
    while ($titre_cleaned) {
	foreach $k (keys %ploi) {
	    $_ = $k;
	    s/[^a-z]+/ /g;
	    if (/$titre_cleaned/) {
		$ploi{$titre} = $ploi{$k};
		return $ploi{$k};
	    }
	}
	$titre_cleaned =~ s/^\s?\S+\s*//;
    }
    $ploi{$titre} = '';
    return ;
}


$cpt = 0;
sub checkout {
    $cpt+=10;
    $contexte = $titre1;
    if ($titre2) {
	$contexte .= ' > '.$titre2;
    }
    $out =  '{"contexte": "'.$contexte.'", "intervention": "'.$intervention.'", "timestamp": "'.$cpt.'", "date": "'.$date.'", "source": "'.$source.'", "heure":"'.$heure.'", "session": "'.$session.'", ';
    if ($ploi = getProjetLoi($titre1)) {
	$out .= "\"numeros_loi\": \"$ploi\", ";
    }
    if ($amendements) {
	$out .= '"amendements": "'.$amendements.'", ';
    }

    if ($intervenant) {
	if ($intervenant =~ s/( et|, )(\s*M[mes\.]*|)\s*([A-Z].*)//) {
	    print $out.'"intervenant": "'.$3."\"}\n";
	}
	if ($inter2fonction{$intervenant} =~ s/( et|, )(\s*M[mes\.]*|)\s*([A-Z].*)//g) {
	    print $out.'"intervenant": "'.$3."\"}\n";
	    $inter2fonction{$intervenant} = '';
	}
	print $out.'"intervenant": "'.$intervenant.'", "fonction": "'.$inter2fonction{$intervenant}.'", "intervenant_url": "'.$intervenant_url."\"}\n";
    }elsif($intervention) {
	print $out.'"intervenant":"'."\"}\n";
    }else {
	return ;
    }
    $commentaire = "";
    $intervenant = "";
    $intervenant_url = "";
    $intervention = "";
    $amendements = join ',', @pre_amendements;
}

sub setFonction {
    my $fonction = shift;
    my $intervenant = shift;
    my $kfonction = lc($fonction);
    $kfonction =~ s/[^a-z]+/ /gi;
    $intervenant =~ s/\W+$//;
    $fonction2inter{$kfonction} = $intervenant;
#    print "$fonction ($kfonction)  => $intervenant \n";
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
    if ($intervenant =~ s/\,\s*(.*)//) {
	setFonction($1, $intervenant);
    }
    if ($intervenant =~ /^[a-z]/) {
	$intervenant =~ s/^l[ea]\s+//i;
	if ($intervenant =~ /([pP]résidente?|[rR]apporteur[a-zé\s]+)\s([A-Z].*)/) { #\s([A-Z].*)/i) {
	    setFonction($1, $2);
	    return $2;
	}
	$conv = $fonction2inter{$intervenant};
	if (!$conv) {
	    $tmp = $intervenant;
	    $tmp =~ s/[^a-z]+/ /gi;
	    $conv = $fonction2inter{$tmp};
	}
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
		$fonction2inter{lc($intervenant)} = $inter;
		$intervenant = $inter;
	    }
	}
    }
    return $intervenant;
}

$string =~ s/\r//g;
$string =~ s/&nbsp;/ /g;
$string =~ s/\|(\W+)\|/$1/g;
$majIntervenant = 0;
$debut = 0;

$string =~ s/<br>\n*//gi;

#print "$string\n"; exit;

$donetitre1 = 0;
foreach $line (split /\n/, $string)
{
    if ($line =~ /DEBUT_SEANCE|séance est ouverte/) {
	$debut = 1;
    }
    #recherche amendements
    if ($line =~ /\<\!\-\- AMEND_/) {
	@pre_amendements = ();
	while ($line =~ /\<\!\-\- AMEND_(\d+)\D/g) {
	    push @pre_amendements, $1;
	}
    }

    #suppression des commentaires
    $line =~ s/\<\!\-\-[^\>]+\>//g;
    #si deux intervenant en même temps
    $line =~ s/\|\s*et\s*\|/ et /gi;
    #si italique ou gras sans raison on supprime
    $line =~ s/\/\s*\// /g;
    $line =~ s/\|\s*\|/ /g;

    #récupère les ancres pour de meilleurs liens sources
    if ($line =~ /\<[a]/i) {
	if ($line =~ s/<a name=["']([^"']+)["'][^<]+<[^>]+>/<</g) {
	    $source = $url."#$1";
	}
    }

    if ($line =~ /<h[1-9]+/i || $line =~ /"(sompresidence|sstitreinfo)"/) {
	if ($line =~ /présidence de (M[^<]+)</i) {
	    $prez = $1;
#	    print "Présidence de $prez\n";
	    if ($prez =~ /^Mm/) {
		setFonction('présidente', $prez);
	    }else {
		setFonction('président', $prez);
	    }
	}elsif($line =~ /h2 class="titre[23]"><*([^<\(]+)\s*/ || $line =~ /class="sstitreinfo">\/([^\/]+)\//) {
	    checkout();
	    if ($1 !~ /suspension/ && $1 !~ /séance/) {
		$titre2 = $1;
	    }
	    $titre2 =~ s/\s+$//;
	    $amendements = @pre_amendements = ();
	    $line = "<p>|$titre2|</p>";
	    $donetitre1 = 0;
	}elsif(!$donetitre1 && $line =~ /h2 class="titre1"><*([^<]+)/) {
	    checkout();
	    $titre = $1;
	    $titre =~ s/[\(\/][^\)\/]+[\)\/]//;
	    if ($titre =~ /^[\/\s]*[\wéè]+ \s*partie[\/\s]*(suite[\/\s]*|)$/i) {
		next
	    }
	    $donetitre1 = 1;
	    $titre1 = $titre;
	    $titre2 = '';
	    $amendements = @pre_amendements = ();
	    $line = "<p>|$titre1|</p>";
	}elsif($line =~ /h1 class="seance"/) {
	    if ($line =~ /(\d{1,2})[ermd]*\s+([a-zéù]+)\s+(\d{4})/) {
		$date = $3.'-'.$mois{$2}.'-'.sprintf('%02d', $1);
	    }
	}elsif($line =~ /h5 class="numencad"/) {
	    $donetitre1 = 0;
	}
    }

    next unless ($debut);

    $line =~ s/<<//g;

#    print "$titre1 > $titre2 : $line\n" ; next;

    if ($line =~ /\<[p]/i) {
	$last_href = '';
	if ($line =~ /href=["']([^"']+)["']/) {
	    $last_href = $1;
	}
	$line =~ s/\s*\<\/?[^\>]+\>//g;
	last if ($line =~ /^\|annexe/i);
	next if ($line !~ /\w/);

	#si italique ou tout gras => commentaire
	if ($line =~ /^\s*\|.*\|\s*$/ || $line =~ /^\s*\/.*\/\s$/) {
	    checkout() if ($intervenant);
	}elsif ($line =~ s/^\s*\|\s*(M[^\|]+)[\|]// ) {
	    checkout();
	    $majIntervenant = 1;
	    $intervenant = setIntervenant($1);
	    $intervenant_url = $last_href;
	    $found = 1;
	}elsif ($line =~ /^\s*\|/) {
	    checkout() if ($intervenant);
	}
	$line =~ s/^\s+//;
	$line =~ s/[\|\/]//g;
	$line =~ s/^[\.\:]\s*//;
	$intervention .= "<p>$line</p>";
    }
}
checkout();
