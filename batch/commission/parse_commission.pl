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
	print '"'.$intervenant.'": "';
#	print $intervention;
	print '"'."\n";
    }elsif($intervention) {
	print '"comment": "';
#	print $intervention;
	print "\"\n";
    }
    $commentaire = "";
    $intervenant = "";
    $intervention = "";
}

sub setIntervenant {
    my $intervenant = shift;
    $intervenant =~ s/[\|\/]//g;
    $intervenant =~ s/\s*\&\#8211\;\s*$//;
    $intervenant =~ s/\s*[\.\:]\s*$//;
    $intervenant =~ s/Madame/Mme/;
    $intervenant =~ s/Monsieur/M./;
    $intervenant =~ s/^M[\.mes]*\s//i;
    $intervenant =~ s/L([ea])\s/l$1 /i;
    $intervenant =~ s/\s+\(/, /g;
    $intervenant =~ s/\)//g;
    $intervenant =~ s/^\s+//;
    $intervenant =~ s/É+/é/gi;
    $intervenant =~ s/\&\#8217\;/'/g;
    if ($intervenant =~ s/\, (.*)//) {
	$fonction = lc($1);
	$fonction =~ s/[^a-z]+/ /gi;
	$fonction2inter{$fonction} = $intervenant;
	if (!$inter2fonction{$intervenant}) {
	    $inter2fonction{$intervenant} = $fonction;
	}
    }
    if ($intervenant =~ /^[a-z]/) {
	$intervenant =~ s/^l[ea]\s+//i;
	if ($intervenant =~ /(présidente?|rapporteur[^A-Z]+)\s([A-Z].*)/) {
	    $intervenant = $2;
	    $fonction2inter{$fonction} = $intervenant;
	    return $2;
	}
	$intervenant = lc($intervenant);
	$conv = $fonction2inter{$intervenant};
#	print "conv: '$conv' '$intervenant'\n";
	if ($conv) {
	    $intervenant = $conv;
	}else {
	    $test = $intervenant;
	    $test =~ s/[^a-z]+/ /gi;
	    foreach $fonction (keys %fonction2inter) {
		if ($fonction =~ /$test/) {
		    $intervenant = $fonction2inter{$fonction};
		    last;
		}
		if ($test =~ /$fonction/) {
		    $intervenant = $fonction2inter{$fonction};
		    last;
		}
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
	    $fonction2inter{lc($2)} = $1;
	}elsif ($line =~ /rapport de \|?M[me\.]+\s([^,\.\;\|]+)[\,\.\;\|]/i) {
	    $fonction2inter{'rapporteur'} = $1; 
	}
    }
}

$string =~ s/\r//g;
$string =~ s/&nbsp;/ /g;
$string =~ s/\|(\W+)\|/$1/g;
foreach $line (split /\n/, $string)
{
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
	}elsif ($line =~ s/^\|([^\|]+)[\|\.]// ) {
	    if ($1 !~ /article|loi|amendement/i) {
		checkout();
		$intervenant = setIntervenant($1);
	    }
	}
	$line =~ s/^\s+//;
	$line =~ s/[\|\/]//g;
	$line =~ s/^[\.\:]\s*//;
	$intervention .= "<p>$line</p>\n";
	if ($line =~ /séance est levée/) {
	    last;
	}
    }elsif ($line =~ /h[125]/i) {
	rapporteur();
	print "$line\n";
    }
}
checkout();
