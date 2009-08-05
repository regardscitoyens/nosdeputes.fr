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
$string =~ s/(\<p class="presente".*)\s*\<br[\/]?\>\s*[\n]?\s*(.*)/\1, \2/g;
$string =~ s/\<br\>.*\n//g;
$string =~ s/&#8217;/'/g;
$string =~ s/&#339;/oe/g;
$string =~ s/&#8211;/-/g;
close FILE;

my %amdmt;
my $expose = 0;
my $presente = 0;
my $identiques =0;

sub numero {
    $line =~ s/^.*content="//; 
    $line =~ s/".*$//;
    $line =~ s/[\(\)]//g;
    if ($line =~ /^\s*(\d+)\s+([1-9a-zA-Z].*)$/i) {
	$amdmt{'numero'} = $1;
	$suite = $2;
	if (!$suite =~ /rect/i) {
	    $amdmt{'rectif'} = 0;
	} else {
	    if ($suite =~ /(\d+)/) {
		$amdmt{'rectif'} = $1;
	    } else {
		$amdmt{'rectif'} = 1;
	    } 
	    if ($suite =~ /bis/i) {
		$amdmt{'rectif'}++;
	    }
     	}
     } else {
         $amdmt{'numero'} = $line;
	 $amdmt{'rectif'} = 0;
     }
}

sub auteurs {
    $line =~ s/\<br\/\>/, /g;
    $line =~ s/\s*\<\/?[^\>]+\>//g;
    $line =~ s/\s+e{1,2}t\s+/, /g;
    $line =~ s/^et\s+/, /g;
    $line =~ s/\s*EXPOSÉ SOMMAIRE\s*//g;
    $amdmt{'auteurs'} = $amdmt{'auteurs'}.", ".$line;
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

sub identiques {
    if ($line =~ /\<div\>\s*de\s*(.*)\s*\<\/div\>/) {
	$line = $1;
	if ($amdmt{'numero'} != $amdmt{'fin_serie'}) {
	    auteurs();
	}
    } else {
	$line =~ s/\s*\<\/?[^\>]+\>//g;
	if ($line =~ /.*Adt\s*.*[°\s](\d+)\s+.*de\s*(.*)$/) {
    	    $num = $1;    	
	    $line = $2;
	    if ($amdmt{'numero'} != $num) {
	    	$amdmt{'fin_serie'} = $num;
	    	auteurs();
	    }
	}
    }
}



$string =~ s/\r//g;
$string =~ s/&nbsp;/ /g;
$string =~ s/\|(\W+)\|/$1/g;
foreach $line (split /\n/, $string)
{
    if ($line =~ /meta.*content=/) {
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
    } elsif ($presente == 1 && $line =~ /class="tirets"/i) {
	    $presente = 2;
    }
    if ($line =~ /(NOEXTRACT|EXPOSE)/i) {
	if (!$amdmt{'numero'} && ($line =~ /class="numamendement"/i || $line =~ /class="titreamend".*num_partie/i)) {
	    if ($line =~ /\<num_amend\>\s*(.*)\s*\<\/num_amend\>/i) { 
	    	$line = $1;
	    	numero();
	    } elsif ($line =~ /\<\/num_partie\>\s*-\s*(.*)\<\/p\>/i) {
		$line = $1;
		numero();
	    } 
	} elsif (!$amdmt{'loi'} && $line =~ /class="titreinitiative"/i) {
	    if ($line =~ /\<num_init\>\s*(\d+)\s*\<\/num_init\>/i) {
		$amdmt{'loi'} = $1;
	    } elsif ($line =~ /\(\s*n.*(\d+).*\)/) {
		$amdmt{'loi'} = $1;
	    }
	} elsif ($line =~ /class="presente"/i) {
	    if ($line =~ /amendement/) {
		$line =~ /(\d+)/;
		$amdmt{'parent'} = $1;
	    } else {
		auteurs();
	    }
	} elsif ($line =~ /class="amddispotexte"/i) {
	    texte();
	} elsif ($line =~ /class="amdexpotitre"/i) {
	    $expose = 1;
	} elsif ($line =~ /class="amdexpotexte"/i) {
	    expose();
	} elsif ($line =~ /amendements\s*identiques/i) {
	    $identiques = 1;
	} elsif ($line =~ /\<div.*\>.*M[\.Mml]/ && !($line =~ /EXPOSE SOMMAIRE/i)) {
	    if ($identiques == 1) {
		identiques();
	    } elsif ($presente == 1) {	
		auteurs();
	    }
	} elsif ($identiques == 1 && $line =~ /\<div\>(\d+)\<\/div\>/) {
	    $amdmt{'fin_serie'} = $1;
	}
    } elsif ($presente == 1 && $line =~ /\<p style=".*text-indent:.*\>.*M[\.Mml]/i) { 
		auteurs();
    } elsif ($line =~ /\<p style=".*text-indent:/i) {
	if ($line =~ /amendement.*(irrecevable|retir)/i) {
	    if (!$amdmt{'sort'}) {
		if ($line =~ /irrecevable/i) {
		    $amdmt{'sort'} = "Irrecevable";
		} elsif ($line =~ /retir/i) {
		    $amdmt{'sort'} = "Retiré avant séance";
		}
	    }
	}
	texte();
    }
}

$amdmt{'auteurs'} =~ s/^\s*,\s*//g;
$amdmt{'auteurs'} =~ s/,\s*$//g;
$amdmt{'auteurs'} =~ s/,+/,/g;
$amdmt{'auteurs'} =~ s/\s+Mme,\s*/ Mme /g;
$amdmt{'auteurs'} =~ s/([a-z])\s+(M[\.Mml])/\1, \2/g;
$amdmt{'auteurs'} =~ s/M\./M /g;
$amdmt{'auteurs'} =~ s/\s*[,]?\s*[rR]apporteur[\s,a-zéèeêà']*/, /g;
$amdmt{'auteurs'} =~ s/\s*[,]?\s*les\s+[cC]ommissaires.*$//g;
$amdmt{'auteurs'} =~ s/,\s*,/,/g;
$amdmt{'auteurs'} =~ s/\s*,\s*$//g;
     
print '"legislature": "'.$amdmt{'legislature'}.'", "loi": "'.$amdmt{'loi'}.'", "numero": "'.$amdmt{'numero'}.'", "fin_serie": "'.$amdmt{'fin_serie'}.'", "rectif": "'.$amdmt{'rectif'}.'", "parent": "'.$amdmt{'parent'}.'", "date": "'.$amdmt{'date'}.'", "auteurs": "'.$amdmt{'auteurs'}.'", "sort": "'.$amdmt{'sort'}.'", "sujet": "'.$amdmt{'sujet'}.'", "texte": "'.$amdmt{'texte'}.'", "expose": "'.$amdmt{'expose'}.'" } '."\n";
