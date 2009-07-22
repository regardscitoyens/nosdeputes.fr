#!/usr/bin/perl

$file = $url = shift;
#use HTML::TokeParser;
$url =~ s/^[^\/]+\///;
$url =~ s/_/\//g;
$source = $url;

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
    $cpt+=10;
    $out =  '{"commission": "'.$commission.'", "intervention": "'.$intervention.'", "timestamp": "'.$cpt.'", "date": "'.$date.'", "source": "'.$source.'", "heure":"'.$heure."\", ";
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
    my $intervenant = shift;
    my $kfonction = lc($fonction);
    $kfonction =~ s/[^a-z]+/ /gi;
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
    $intervenant =~ s/[\|\/]//g;
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
	if ($intervenant =~ /([pP]résidente?|[rR]apporteur[a-zé\s]+)\s([A-Z].*)/) { #\s([A-Z].*)/i) {
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
$string =~ s/\|(\W+)\|/$1/g;
$majIntervenant = 0;
$body = 0;

$string =~ s/<br>\n//gi;

foreach $line (split /\n/, $string)
{
    if ($line =~ /<body>/) {
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
	}
    }
    if ($line =~ /\<[p]/i) {
	$found = 0;
	$line =~ s/\s*\<\/?[^\>]+\>//g;
	last if ($line =~ /^\|annexe/i);
	next if ($line !~ /\w/);

	#si italique ou tout gras => commentaire
	if ($line =~ /[\|\/]\s*$/) {
	    checkout() if ($intervenant);	    
	    rapporteur();
	    $found = 1;
	}elsif ($line =~ s/^\|(M[^\|]+)[\|]// ) {
	    checkout();
	    $majIntervenant = 1;
	    $intervenant = setIntervenant($1);
	    $found = 1;
	}
	$line =~ s/^\s+//;
	$line =~ s/[\|\/]//g;
	$line =~ s/^[\.\:]\s*//;
	if (!$majIntervenant && !$found) {
	    if     ($line =~ s/^\s*(M[mes\.]+\s[^\.]+)\.//) {
		checkout();
		$intervenant = setIntervenant($1);		
	    }elsif ($line =~ s/^\s*(M[mes\.]+\s[A-Z][^\s\,]+\s*([A-Z][^\s\,]+\s*|de\s*){2,})// ) {
		checkout();
		$intervenant = setIntervenant($1);
	    }
	}
	$intervention .= "<p>$line</p>";
	if ($line =~ /séance est levée|Informations? relatives? à la Commission/i) {
	    last;
	}
    }elsif ($line =~ /<h[1-9]+/i) {
	rapporteur();
#	print "$line\n";
	if ($line =~ /SOMdate/) {
	    if ($line =~ /\w+\s+(\d+)[erme]*\s+([^\s\d]+)\s+(\d+)/) {
		$date = sprintf("%04d-%02d-%02d", $3, $mois{$2}, $1);
	    }
	}elsif ($line =~ /SOMseance/i) {
	    if ($line =~ /(\d+) heures?( \d+|)/i) {
		$heure = sprintf("%02d:%02d", $1, $2 || "00");
	    }
	}elsif(!$commission && $line =~ /commission/i) {
	    if ($line =~ /\>\|?(Comm[^\>\|]+)[\<\|]/) {
		$commission = $1;
	    }
	}
    }
}
checkout();
