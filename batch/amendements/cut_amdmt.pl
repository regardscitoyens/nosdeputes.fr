#!/usr/bin/perl

$file = shift;
use HTML::TokeParser;

$id = $file;
$id =~ s/^.*\d{4}_//;
$id =~ s/\.asp$//;

open(FILE, $file) ;
@string = <FILE>;
$string = "@string";
utf8::decode($string);
$string =~ s/&#8217;/'/g;
$string =~ s/&#339;/oe/g;
$string =~ s/&#8211;/-/g;
close FILE;

my %amdmt;

sub num {
    if ($line =~ /NUM_AMEND/) {
	$line =~ s/^.*\<NUM_AMEND\>//;
	$line =~ s/\<\/NUM_AMEND.*$//;
	$line =~ s/\s*\<\/?[^\>]+\>//g;
    	$amdmt{'num'} = $line;
    }
}

sub auteurs {
    $line =~ s/\s*\<\/?[^\>]+\>//g;
    $amdmt{'auteurs'} = $line;
}

sub titre {
    $line =~ s/\s*\<\/?[^\>]+\>//g;
    $amdmt{'titre'} = $line;
}

sub texte {
    $line =~ s/\s*\<\/?[^\>]+\>//g;
    $amdmt{'texte'} = $amdmt{'texte'}.'    '.$line;
}

sub expose {
    $line =~ s/\s*\<\/?[^\>]+\>//g;
    $amdmt{'expose'} = $amdmt{'expose'}.'    '.$line;
}



$string =~ s/\r//g;
$string =~ s/&nbsp;/ /g;
$string =~ s/\|(\W+)\|/$1/g;
foreach $line (split /\n/, $string)
{
    if ($line =~ /(NOEXTRACT|EXPOSE)/) {
	if ($line =~ /class="numamendement"/) { 
	    num();
	}elsif ($line =~ /class="presente"/) {
	    auteurs();
	}elsif ($line =~ /class="amddispotitre"/) {
	    titre();
	}elsif ($line =~ /class="amddispotexte"/) {
	    texte();
	}elsif ($line =~ /class="amdexpotexte"/) {
	    expose();
	}
    }
}

print "******************************************************\n";
print "Admt $amdmt{'num'} de $amdmt{'auteurs'} - $amdmt{'titre'}\n";
print "******************************************************\n";
print "$amdmt{'texte'}\n";
print "\nEXPOSE : $amdmt{'expose'}\n";

