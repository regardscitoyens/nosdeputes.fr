#!/usr/bin/perl

$file = shift;
use HTML::TokeParser;
use File::stat;
use Date::Format;
require ("../common/common.pm");

my %amdmt;

$source = $file;
$source =~ s/^.*(http.*)$/\1/i;
$source =~ s/_/\//g;

# Récupération des informations identifiantes à partir de l'url plus sure :
if ($source =~ /(\d{2})\/amendements\/(\d{4})\/(\d{4})(\d|[A-Z])(\d{4})\./i) {
  $amdmt{'legislature'} = $1;
  if ($2-$3 == 0) {
    $amdmt{'loi'} = $3+0;
  }
  $num = $5+0;
  $lettre = $4;
  if ($lettre =~ /[a-z]/i) {
    $amdmt{'numero'} = $num.uc($lettre);
  } else {
    $amdmt{'numero'} = (10000*$lettre+$num);
  }
} elsif ($source =~ /(\d{2})\/amendements\/(\d{4})\/(\d+)\./i) {
  $amdmt{'legislature'} = $1;
  $amdmt{'loi'} = $2+0;
  $num = $3+0;
  $amdmt{'numero'} = $num;
}

open(FILE, $file) ;
@string = <FILE>;
$string = "@string";
utf8::decode($string) if ($string =~ /charset=UTF-?8/i);
$string =~ s/(\<p class="presente".*)\s*\<br[\/]?\>\s*[\n]?\s*(.*)/\1, \2/g;
$string =~ s/\<br\>.*\n//g;
$string =~ s/(&#8217;|’)/'/g;
$string =~ s/&#339;/oe/g;
$string =~ s/&#8211;/-/g;
$string =~ s/&Eacute;/É/g;
$string =~ s/&eacute;/é/g;
$string =~ s/&Egrave;/È/g;
$string =~ s/&egrave;/è/g;
$string =~ s/&Agrave;/À/g;
$string =~ s/&agrave;/à/g;
$string =~ s/\\//g;
close FILE;

my $presente = 0;
my $texte = 0;
my $identiques = 0;
my $num_ident = -1;

sub numero {
    $line =~ s/^.*content="//; 
    $line =~ s/".*$//;
    $line =~ s/[\(\)]//g;
    if ($line =~ /^\s*(\d+)\s+([1-9a-zA-Z].*)$/i) {
      # $amdmt{'numero'} = $1;
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
	$line =~ /(\d+)/;
    #   $amdmt{'numero'} = $1;
	$amdmt{'rectif'} = 0;
     }
}

sub auteurs {
    $line =~ s/\<br\/\>/, /g;
    $line =~ s/\s*\<\/?[^\>]+\>//g;
    $line =~ s/\s*présenté\s*par\s*//g;
    $line =~ s/\s+e{1,2}t\s+/, /g;
    $line =~ s/^et\s+/, /g;
    $line =~ s/\s*EXPOSÉ SOMMAIRE\s*//g;
    $line =~ s/\s+,/,/g;
    $line =~ s/^de M/M/;
    if (!$line =~ /^$/ && (!$amdmt{'auteurs'} || $amdmt{'auteurs'} !~  /, $line/)) {
	$amdmt{'auteurs'} = $amdmt{'auteurs'}.", ".$line;
    }
}

sub texte {
    $line2 = $line;
    $line2 =~ s/\s*\<\/?[^\>]+\>//g;
    $line2 =~ s/^[ \s]+(\w)/\1/;
    $line2 =~ s/[\s ]+$//;
    if ($line2 !~ /^$/ && !($line2 =~ /\s*Adt\s+n°\s*$/)) {
    	$output = 'texte';
    	if ($texte == 2) {
           return if $line2 =~ /^\s*\d+\s*$/;
           $output = 'expose';
        }
        $line2 =~ s/"\s*([^"]*)\s*"/« \1 »/g;
    	if (!$amdmt{$output}) { $amdmt{$output} = "<p>".$line2."</p>"; }
    	else { $amdmt{$output} = $amdmt{$output}."<p>".$line2."</p>"; }
    }
}

sub sortseance {
    if ($line =~ /irrecevable/i) {
	$amdmt{'sort'} = 'Irrecevable';
    } elsif ($line =~ /retir.+s.+ance/i) {
	$amdmt{'sort'} = 'Retiré avant séance';
    } elsif ($line =~ /retiré/i) {
	$amdmt{'sort'} = 'Retiré';
    } elsif ($line =~ /non.*(soutenu|défendu)/i) {
	$amdmt{'sort'} = 'Non soutenu';
    } elsif ($line =~ /tomb/i) {
	$amdmt{'sort'} = 'Tombe';
    } elsif ($line =~ /rejet/i) {
	$amdmt{'sort'} = 'Rejeté';
    } elsif ($line =~ /adopt/i) {
	$amdmt{'sort'} = 'Adopté';
    } elsif ($line =~ /Re/) {
        $amdmt{'sort'} = 'Retiré';
    }
}

sub identiques {
    if ($line =~ /\<div\>\s*(de)?\s*(M[\s\.Mml].*)\s*\<\/div\>/) {
	$line = $2;
	if ($amdmt{'numero'} != $num_ident) {
	    auteurs();
	}
    } else {
	$line =~ s/\s*\<\/?[^\>]+\>//g;
	if ($line =~ /^.*Adt\s+n°\s+(\d+).*\s+de\s+(.*)\s*$/) {
    	    $num = $1;
	    $line = $2;
	    if ($num_ident == -1) {
		$num_ident = $num;
		$amdmt{'serie'} = $num_ident."-";
	    }
	    if ($amdmt{'numero'} != $num) {
		auteurs();
	    }
	    if ($num > $num_ident + 1) {
		$amdmt{'serie'} = $amdmt{'serie'}.$num_ident.",".$num."-";
	    }
	    $num_ident = $num;
	} elsif ($line =~ /^\s*(\d+)\s*$/) {
    	    $num = $1;
	    if ($num_ident == -1) {
		$num_ident = $num;
		$amdmt{'serie'} = $num_ident."-";
	    }
	    if ($num > $num_ident + 1) {
		$amdmt{'serie'} = $amdmt{'serie'}.$num_ident.",".$num."-";
	    }
	    $num_ident = $num;
	} elsif ($line =~ /^\s*de\s+(M.*)\s*$/) {
    	    $line = $1;
	    if ($amdmt{'numero'} != $num) {
		auteurs();
	    }
	    $num_ident = $num;
	}
    }
}



$string =~ s/\r//g;
$string =~ s/\t+/ /g;
$string =~ s/ +\n+/\n/g;
$string =~ s/\n+ +/\n/g;
$string =~ s/&nbsp;/ /g;
$string =~ s/\|(\W+)\|/$1/g;
$string =~ s/([^\n>]+)\n/\1 /g;
$string =~ s/>\n([^\n<]+)/> \1/g;
foreach $line (split /\n/, $string)
{
#print "TEST: $presente / $texte / $line\n";
    if ($line =~ /meta.*content=/) {
	if ($line =~ /name="DATE_BADAGE"/i) { 
	    $line =~ s/^.*content="//i; 
	    $line =~ s/".*$//;
	    if ($line =~ /(\d{1,2})\/(\d{2})\/(\d{4})/) {
		$amdmt{'date'} = $3.'-'.$2.'-'.sprintf('%02d', $1);
	    }
	} elsif ($line =~ /name="DESIGNATION_ARTICLE"/i && !$amdmt{'sujet'}) {
	    $line =~ s/^.*content="//i; 
	    $line =~ s/"\s*(name|\>).*$//;
	    $amdmt{'sujet'} = $line;
	} elsif ($line =~ /name="SORT_EN_SEANCE"/i) { 
	    $line =~ s/^.*content="//i; 
	    $line =~ s/".*$//;
	    sortseance();
	} elsif ($line =~ /name="NUM_AM(TXT|ENDG?)"/i) { 
	    numero();
	}
    } elsif ($line =~ /date_?amend.*([0-9]+e?r? \S+ [0-9]+)\D/i && !$amdmt{'date'}) {
           $amdmt{'date'} = join '-', datize($1);
    } elsif ($line =~ /class="amddispotitre"/i && !$amdmt{'sujet'}) {
            $line =~ s/<[^>]+>//g;
            $amdmt{'sujet'} = $line;
    } elsif (($line =~ /class="presente"/i || $line =~ /<div>\s*(de )?M[Mmel.s]+ /) && $line !~ /utilisateurs/) {
        $texte = 0 if ($texte == 2);
	if ($presente != 1) {
	    $presente = 1;
	} elsif ($texte >= 1 && $line =~ /font-style: italic/i) {
	    texte();
	}
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
        } elsif (!$amdmt{'serie'} && ($line =~ /class="numamendement".*à\s+(\d+)\W/i)) {
          $num_ident = $1;
          $amdmt{'serie'} = ($amdmt{'numero'}+1).'-';
	} elsif ($line =~ /class="presente"/i) {
	    if ($line =~ /amendement/) {
		$line =~ /(\d+)/;
		$amdmt{'parent'} = $1;
	    } elsif ($presente == 1) {
		auteurs();
	    } else {
		texte();
	    }
	} elsif ($line =~ /class="amddispotitre"/i) {
	    $texte = 1;
	    if ($line =~ /amendement.*[\s°](\d+)[\s\<]/i) {
		$amdmt{'parent'} = $1;
	    }
	} elsif ($line =~ /class="amddispotexte"/i) {
	    texte();
	} elsif ($line =~ /class="amdexpotitre"/i) {
	    if ($amdmt{'texte'} || !$line =~ /article/i) {
		$texte = 2;
	    }
	} elsif ($line =~ /class="amdexpotexte"/i) {
	    texte();
	} elsif ($line =~ /amendements\s*identiques/i) {
	    $identiques = 1;
	} elsif ($line =~ /\<div.*\>.*M[\.Mml]/ && !($line =~ /EXPOSE SOMMAIRE/i)) {
	    if ($identiques == 1) {
		identiques();
	    } elsif ($presente == 1) {	
		auteurs();
	    }
	} elsif ($identiques == 1 && $line =~ /\<div\>.*(\d+).*\<\/div\>/) {
		identiques();
	} elsif ($texte >= 1 && $line =~ /\<div\>(.*)\<\/div\>/) {
	    $line = $1;
	    texte();
	}
    } elsif ($line =~ /class="amddispotitre"/i) {
        $texte = 1;
        if ($line =~ /amendement.*[\s°](\d+)[\s\<]/i) {
            $amdmt{'parent'} = $1;
        }
    } elsif ($line =~ /class="amd(expo|dispo)texte"/i) {
        texte();
    } elsif ($line =~ /class="amdexpotitre"/i) {
        if ($amdmt{'texte'} || !$line =~ /article/i) {
            $texte = 2;
        }
    } elsif ((!$amdt{'sort'} || $amdt{'sort'} == "") && ($line =~ /\<div.*id="sort"/i || $line =~ /retir.+ avant s.+ance/i)) {
	sortseance();
    } elsif ($identiques == 1 && $line =~ /\<p style=".*text-align:.*\>.*M[\.Mml]/i) {
	identiques();
    } elsif ($line =~ /class="presente"/i) {
	if ($line =~ /amendement/) {
	    $line =~ /(\d+)/;
	    $amdmt{'parent'} = $1;
	} elsif ($texte < 1) {
	    auteurs() if ($line !~ /par<\/p>/);
	} else {
	    texte();
	}
    } elsif ($presente == 1 && $line =~ /<(p style=".*text-indent:.*|td align="center"[^>]*)>.*(M[\.Mml]|Le gouvern)/i) { 
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
    } elsif ($line =~ /\<p[^\>]*\>(.*)\<\/p\>/i && $texte >= 1) {
	$line = $1;
	texte();
    }
}

if ($num_ident > 0) {
    $amdmt{'serie'} = $amdmt{'serie'}.$num_ident;
}

$amdmt{'auteurs'} =~ s/\s+Mme,\s*/ Mme /g;
$amdmt{'auteurs'} =~ s/([a-z])\s+(M[\.Mml])/\1, \2/g;
$amdmt{'auteurs'} =~ s/,\s*M[\s\.mle]+\s*,/,/g;
$amdmt{'auteurs'} =~ s/\s*[,]?\s*les\s+[cC]ommissaires.*$//g;
$amdmt{'auteurs'} =~ s/\s*[,]?\s*[rR]apporteur[\s,a-zéèêà\-']*M(.*)/, M\1/g;
$amdmt{'auteurs'} =~ s/\s*[,]?\s*[rR]apporteur[\s,a-zéèêà\-']*//g;
$amdmt{'auteurs'} =~ s/(,\s*,|,+)/,/g;
$amdmt{'auteurs'} =~ s/,+/,/g;
$amdmt{'auteurs'} =~ s/^\s*,\s*//g;
$amdmt{'auteurs'} =~ s/\s*,\s*$//g;
$amdmt{'auteurs'} =~ s/ et(M[mle\.\s])/, \1/g;
$amdmt{'auteurs'} =~ s/ et(\W)/\1/g;
if ($amdmt{'auteurs'} =~ /^(.*)[,\s]+(le(s\s*membres\s*du)?.*groupe.*)$/) {
  $amdmt{'auteurs'} = $1;
  $gpe = $2;
  $amdmt{'auteurs'} =~ s/[,\s]+$//;
  $gpe =~ s/\s*,\s*/ /g;
  $gpe =~ s/\s+/ /g;
  $amdmt{'auteurs'} .= ", ".$gpe
}

if (!$amdmt{'date'}) {
  $time = (stat $file)[9];
  $amdmt{'date'} = time2str("%Y-%m-%d", $time);
}
if (!$amdmt{'sort'} && $amdmt{'texte'} =~ /amendement irrecevable/i) {
  $amdmt{'sort'} = 'Irrecevable';
}

print '{"source": "'.$source.'", "legislature": "'.$amdmt{'legislature'}.'", "loi": "'.$amdmt{'loi'}.'", "numero": "'.$amdmt{'numero'}.'", "serie": "'.$amdmt{'serie'}.'", "rectif": "'.$amdmt{'rectif'}.'", "parent": "'.$amdmt{'parent'}.'", "date": "'.$amdmt{'date'}.'", "auteurs": "'.$amdmt{'auteurs'}.'", "sort": "'.$amdmt{'sort'}.'", "sujet": "'.$amdmt{'sujet'}.'", "texte": "'.$amdmt{'texte'}.'", "expose": "'.$amdmt{'expose'}.'" } '."\n";
