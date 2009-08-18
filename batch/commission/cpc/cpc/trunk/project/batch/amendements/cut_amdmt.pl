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
$string =~ s/\<br\>.*\n//g;
$string =~ s/&#8217;/'/g;
$string =~ s/&#339;/oe/g;
$string =~ s/&#8211;/-/g;
close FILE;

my %amdmt;

sub auteurs {
    $line =~ s/\s*\<\/?[^\>]+\>//g;
    $amdmt{'auteurs'} = $amdmt{'auteurs'}.$line;
}

sub texte {
    $line =~ s/\s*\<\/?[^\>]+\>//g;
    $output = 'texte';
    if ($expose) { $output = 'expose'; }
    if ($amdmt{$output} =~ /^$/) { $amdmt{$output} = "<p>".$line."</p>"; }
    else { $amdmt{$output} = $amdmt{$output}."<p>".$line."</p>"; }
}

sub expose {
    $line =~ s/\s*\<\/?[^\>]+\>//g;
    if ($amdmt{'expose'} =~ /^$/) { $amdmt{'expose'} = "<p>".$line."</p>"; }
    else { $amdmt{'expose'} = $amdmt{'expose'}."<p>".$line."</p>"; }
}



$string =~ s/\r//g;
$string =~ s/&nbsp;/ /g;
$string =~ s/\|(\W+)\|/$1/g;
$expose = false;
foreach $line (split /\n/, $string)
{
    if ($line =~ /meta/) {
	if ($line =~ /name="LEGISLATURE"/) { 
	    $line =~ s/^.*content="//; 
	    $line =~ s/".*$//;
	    $amdmt{'legislature'} = $line;
	} elsif ($line =~ /name="DATE_BADAGE"/) { 
	    $line =~ s/^.*content="//; 
	    $line =~ s/".*$//;
	    $amdmt{'date'} = $line;
	} elsif ($line =~ /name="DESIGNATION_ARTICLE"/) { 
	    $line =~ s/^.*content="//; 
	    $line =~ s/".*$//;
	    $amdmt{'sujet'} = $line;
	} elsif ($line =~ /name="SORT_EN_SEANCE"/) { 
	    $line =~ s/^.*content="//; 
	    $line =~ s/".*$//;
	    $amdmt{'sort'} = $line;
	} elsif ($line =~ /name="NUM_INITG"/) { 
	    $line =~ s/^.*content="//; 
	    $line =~ s/".*$//;
	    $amdmt{'loi'} = $line;
	} elsif ($line =~ /name="NUM_AMENDG"/) { 
	    $line =~ s/^.*content="//; 
	    $line =~ s/".*$//;
	    $amdmt{'numero'} = $line;
	}
    }
    if ($line =~ /(NOEXTRACT|EXPOSE)/) {
	if ($line =~ /class="presente"/) {
	    auteurs();
	}elsif ($line =~ /class="amddispotexte"/) {
	    texte();
	}elsif ($line =~ /class="amdexpotitre"/) {
	    $expose = true;
	}elsif ($line =~ /class="amdexpotexte"/) {
	    expose();
	}
    }
}

print '{ ';
foreach $k (keys %amdmt) {
    if (lc($k) =~ /texte/) { print '"'.lc($k).'": "'.$amdmt{$k}.'" } '."\n"; }
    else { print '"'.lc($k).'": "'.$amdmt{$k}.'", '; }
}
