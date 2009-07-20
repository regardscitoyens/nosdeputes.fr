#!/usr/bin/perl

$file = shift;
use HTML::TokeParser;

open(FILE, $file) ;
@string = <FILE>;
$string = "@string";
close FILE;

$string =~ s/<\/?b>/|/g;
$string =~ s/<\/?i>/\//g;
$string =~ s/\r//g;

#utf8::decode($string);
#
#$p = HTML::TokeParser->new(\$string);
#
#while ($t = $p->get_tag('p', 'h1', 'h5')) {
#    print "--".$p->get_text('/'.$t->[0])."\n";
#}
#
#exit;
sub checkout {
    chop($intervention);
    if ($intervenant) {
	if ($intervenant =~ s/ et M[mes\.]* (.*)//) {
	    print '"'.$1.'": "'.$intervention."\"\n";
	}
	print '"'.$intervenant.'": "';
    }elsif($intervention) {
	print '"comment": "';
	
    }else {
	return ;
    }
#    print $intervention;
    print '"'."\n";
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
    if ($line =~ /\<[p]/i) {
	$line =~ s/\s*\<\/?[^\>]+\>//g;
	last if ($line =~ /^\|annexe/i);
	next if ($line !~ /\w/);

	#si italique ou tout gras => commentaire
	if ($line =~ /[\|\/]\s*$/) {
	    checkout if ($intervenant);	    
	    rapporteur();
	}elsif ($line =~ s/^\|(M[^\|]+)[\|]// ) {
	    checkout();
	    $majIntervenant = 1;
	    $intervenant = setIntervenant($1);
	}elsif (!$majIntervenant && $line =~ s/^(M[mes\.]+\s[A-Z][^\s\,]+\s*([A-Z][^\s\,]+\s*|de\s*){2,})// ) {
	    checkout();
	    $intervenant = setIntervenant($1);
	}
	$line =~ s/^\s+//;
	$line =~ s/[\|\/]//g;
	$line =~ s/^[\.\:]\s*//;
	$intervention .= "<p>$line</p>\n";
	if ($line =~ /séance est levée|Informations? relatives? à la Commission/i) {
	    last;
	}
    }elsif ($line =~ /<h[1-9]+/i) {
	rapporteur();
	print "$line\n";
    }
}
checkout();
