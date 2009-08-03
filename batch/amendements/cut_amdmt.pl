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
my $expose = 0;
my $presente = 0;

sub numero {
    $line =~ s/^.*content="//; 
    $line =~ s/".*$//;
    if ($line =~ /(\d+).*(\d).*rect/) {
	 $amdmt{'numero'} = $1;
	 $amdmt{'rectif'} = $2;
     } elsif ($line =~ /(\d+).*rect/) {
         $amdmt{'numero'} = $1;
	 $amdmt{'rectif'} = 1;
     } else {
         $amdmt{'numero'} = $line;
	 $amdmt{'rectif'} = 0;
     }
}

sub auteurs {
    $line =~ s/\s*\<\/?[^\>]+\>//g;
    $line =~ s/ et /, /g;
    $amdmt{'auteurs'} = $amdmt{'auteurs'}.$line;
}

sub texte {
    $line =~ s/\s*\<\/?[^\>]+\>//g;
    $output = 'texte';
    if ($expose == 1) { $output = 'expose'; }
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
foreach $line (split /\n/, $string)
{
    if ($line =~ /meta/) {
	if ($line =~ /name="LEGISLATURE"/i) { 
	    $line =~ s/^.*content="//i; 
	    $line =~ s/".*$//;
	    $amdmt{'legislature'} = $line;
	} elsif ($line =~ /name="DATE_BADAGE"/i) { 
	    $line =~ s/^.*content="//i; 
	    $line =~ s/".*$//;
	    if ($line =~ /(\d{1,2})\/(\d{2})\/(\d{4})/) {
		$amdmt{'date'} = $3.'-'.$2.'-'.sprintf('%02d', $1);
	    }
	} elsif ($line =~ /name="DESIGNATION_ARTICLE"/i) { 
	    $line =~ s/^.*content="//i; 
	    $line =~ s/".*$//;
	    $amdmt{'sujet'} = $line;
	} elsif ($line =~ /name="SORT_EN_SEANCE"/i) { 
	    $line =~ s/^.*content="//i; 
	    $line =~ s/".*$//;
	    $amdmt{'sort'} = $line;
	} elsif ($line =~ /name="NUM_INITG"/i) { 
	    $line =~ s/^.*content="//i; 
	    $line =~ s/".*$//;
	    $amdmt{'loi'} = $line;
	} elsif ($line =~ /name="NUM_AMENDG"/i) { 
	    numero();
	}
    }
    if ($line =~ /class="presente"/i) {
	    $presente = 1;
    }
    if ($line =~ /(NOEXTRACT|EXPOSE)/i) {
	if (!$amdmt{'numero'} && $line =~ /class="numamendement"/i) { 
	    $line =~ s/^.*\<num_amend\>//i; 
	    $line =~ s/\<\/num_amend\>.*$//i;
	    numero();
	} elsif (!$amdmt{'loi'} && $line =~ /class="titreinitiative"/i) { 
	    $line =~ s/^.*\<num_init\>//i; 
	    $line =~ s/\<\/num_init\>.*$//i;
	    $amdmt{'loi'} = $line;
	} elsif ($line =~ /class="presente"/i) {
	    auteurs();
	} elsif ($line =~ /div.*M/ && $presente == 1) {
	    auteurs();
	} elsif ($line =~ /class="tirets"/i) {
	    $presente = 2;
	} elsif ($line =~ /class="amddispotexte"/i) {
	    texte();
	} elsif ($line =~ /class="amdexpotitre"/i) {
	    $expose = 1;
	} elsif ($line =~ /class="amdexpotexte"/i) {
	    expose();
	}
    } elsif ($line =~ /\<p style="text-indent:/i) {
	if ($line =~ /amendement.*irrecevable.*application/i) {
	    if (!$amdmt{'sort'}) {
		$amdmt{'sort'} = "Irrecevable";
	    }
	}
	texte();
    }
}

print '{"legislature": "'.$amdmt{'legislature'}.'", "loi": "'.$amdmt{'loi'}.'", "numero": "'.$amdmt{'numero'}.'", "rectif": "'.$amdmt{'rectif'}.'", "date": "'.$amdmt{'date'}.'", "auteurs": "'.$amdmt{'auteurs'}.'", "sort": "'.$amdmt{'sort'}.'", "sujet": "'.$amdmt{'sujet'}.'", "texte": "'.$amdmt{'texte'}.'", "expose": "'.$amdmt{'expose'}.'" } '."\n";
