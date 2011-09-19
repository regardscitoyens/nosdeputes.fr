#!/usr/bin/perl

use URI::Escape;
use HTML::Entities;
use Encode;
use utf8;
require "../common/common.pm";

$| = 1;
$file = shift;
open(FILE, $file);
@doc = <FILE>;
$doc = "@doc";
@doc = ();
close FILE;

$url_source = uri_unescape($file);
$url_source =~ s/.*http/http/;

$session = '';

if ($doc =~ /ance du (\d+e?r? \S+ \d+)/i) {
	@date = datize($1);
	$date = join '-', @date;
	$session = sessionize(@date);
}

$doc =~ s/\n/ /g;
$doc =~ s/&nbsp;/ /gi;
$doc =~ s/.* id="par_1"/<p id="par_1"/;
$doc =~ s/<p class="l1_signature" .*//;
$doc =~ s/<!--[^>]*-->//g;
$doc =~ s/> +</></g;

#$doc =~ s/<\/?(i|cri|div)[^>]*>//ig;
#$doc =~ s/<p[^>]+class="[^"]+_article"[^>]*>[^<]+<\/p>//g;


$doc =~ s/  +/ /g;
$doc =~ s/<\/p>/<\/p>\n/g;

$intervention = '';
$timestamp = 0;
sub print_inter {
	if ($heure && $intervention && $intervention ne "<p></p>") {
		$timestamp += 20;
		$context = $bigcontext;
		$context .= ' > '.$subcontext if ($subcontext);
                if ($intervention =~ /((projet|proposition|motion|lettre)\s[^<]*(n°|n<sup>os?<\/sup>|nos?|n&[^;]+;&[^;]+;)[^<\.]{1,5}\d[^<\.]+)/i) {
                          $docs = $1;
			  $docs =~ s/°//g;
                          $docs =~ s/&[^;]*;//g;
                          $numeros_loi = '';
                          while ($docs =~ /(\d+)([\(\[\, ]+(\d{4}-\d{4})|)/g) {
                                 if ($3) {
                                          $numeros_loi .= law_numberize($1,$3).",";
                                 }else{
                                          $numeros_loi .= law_numberize($1,$session).",";
                                 }
                          }
                          chop($numeros_loi);
                }
                if ($intervention =~ /amendements? n([^<]+)/) {
                                        $amdt = $1;
                                        $amdt =~ s/&[^;]*;//g;
					$amdt =~ s/°//g;
                                        $amendements = '';
                                        while ($amdt =~ /(\d+)( ?rect|)\D/g) {
                                                $amendements .= "$1";
#                                                $amendements .= " rectifié" if ($2);
                                                $amendements .= ",";
				}
				chop($amendements);
			}
		$intervention =~ s/<p> +/<p>/g;
		$secondinter = '';
		$secondinter = $1 if ($inter =~ s/ et (.*)//) ;
		utf8::encode($context);
		utf8::encode($intervention);
		utf8::encode($fonction);
		$json  = '{"contexte": "'.quotize($context).'", "intervention": "'.quotize($intervention).'", "timestamp": "'.$timestamp.'", "date": "'.$date.'", "source": "'.$url_source.$source.'", "heure":"'.$heure.'", "intervenant": "'.name_lowerize($inter).'", "fonction": "'.$fonction.'", "intervenant_url": "'.$url_inter.'", "session":"'.$session.'"';
		$json .= ', "numeros_loi":"'.$numeros_loi.'"' if ($numeros_loi);
		$json .= ', "amendements":"'.$amendements.'"' if ($amendements);
		$json .= "}\n";
		print $json;
		if ($secondinter) {
		$json  = '{"contexte": "'.quotize($context).'", "intervention": "'.quotize($intervention).'", "timestamp": "'.$timestamp.'", "date": "'.$date.'", "source": "'.$url_source.$source.'", "heure":"'.$heure.'", "intervenant": "'.name_lowerize($secondinter).'", "fonction": "", "intervenant_url": "'.$url_inter.'", "session":"'.$session.'"';
                $json .= ', "numeros_loi":"'.$numeros_loi.'"' if ($numeros_loi);
                $json .= ', "amendements":"'.$amendements.'"' if ($amendements);
                $json .= "}\n";
                print $json;
		}
	}
	$intervention = '';
	$inter = '';
	$fonction = '';
	$url_inter = '';
	$amendements = '';
}

foreach (split /\n/, $doc) {
    utf8::decode($_);
    $_ = decode_entities($_);
    if (/<\/span><span([^>]*>)/ && $1 !~ /orateur_qualite/) {
	s/<\/span><span[^>]*>//g;
    }
	if (/name="([^"]+)"/) {
		$source = "#$1";
	}
        if (/<span class="info_entre_parentheses">\((.*)<\/span>([\.\s\)]*)/) {
                $didasc = $1;
                if ($didasc =~ /(ouverte|reprise) (&#224;|à) (\S+ heures\s*\S*)\W/) {
                        $h = heurize($3);
			($htab) = split /:/, $h;
			if (!$heure || ($htab >= 14 && $oldhtab < 14) || ($htab >= 20 && $oldhtab < 20)) {
			    print_inter();
			    $heure = $h;
			    $oldhtab = $htab;
			    $timestamp = 0;
			}
                }
        }
	if (/>[^a-z]*Pr(\&\#[0-9]+\;|é|É)sidence de (M[^<]*)/i) {
		$president = $2;
	}
	next if (!$heure);
	if (/class="intervenant/) {
		if (/class="orateur_nom"[^>]*>([^<]+)</) {
			$tmpinter = $1;
		}elsif(/<a [^>]*>(.+)<\/a>/) {
			$tmpinter = $1;
		}
		$tmpfonction = '';
		$tmpurl_inter = '';
		if (/class="orateur_qualite"[^>]*>([^>]*)</) {
			$tmpfonction = $1;
		}
                if (/href="(\/sen[^"]+)"/i) {
                        $tmpurl_inter = "http://www.senat.fr$1";
                }
                $tmpinter =~ s/<[^>]*>//g;
                $tmpinter =~ s/[\.,]\s*$//;
                $tmpfonction =~ s/[\.,]\s*$//;
		#Cas mauvais formatage des interventions
		if ($tmpinter =~ /^(.{4}[^\(]*[^M])\./) {
			$tmpinter = $1;
			s/$tmpinter/$tmpinter<\/span>/;
		}

                if (($tmpinter =~ /l[ae][ &\#\;0-9]+(pr(\&\#233\;|é|É)sidente?)/i) && $tmpinter !~ /mission/i && $president) {
                        $tmpinter = $president;
                        $tmpfonction = $1;
		}
		if (!$tmpfonction && $tmpinter =~ s/,(.*)//) {
			$tmpfonction = $1;
		}
		if ($tmpinter ne $inter) {
			print_inter();
			$inter = $tmpinter;
			$url_inter = $tmpurl_inter;
			$fonction = $tmpfonction;
		}
	}
        while (s/([^>]*)<span class="info_entre_parentheses">\(([^\)]*)\)?<\/span>([\.\s\)]*)//) {
		$i = $1;
		$i =~ s/<[^>]*>//g;
                $intervention .= "<p>".$1."</p>";
                $didasc = $2;
                $didasc =~ s/<[^>]*>//gi;
                $didasc =~ s/\)//g;
                $predida_inter = $inter;
                $predida_urlinter = $url_inter;
                $predida_fonction = $fonction;
                print_inter();
                $intervention = '<p>'.$didasc.'</p>';
                print_inter();
                $inter = $predida_inter;
                $url_inter = $predida_urlinter;
                $fonction = $predida_fonction;
        }
	if (/class="titre_/) {
		if ($inter) {
                        print_inter();
		}
	} 
	$iscontext = '';
	if (/"titre_S([123][^"]*)"/ || /"mention_(article)"/) {
		$iscontext = $1;
		print_inter();
	}
	if (s/.*id="(intv_|)par_[^>]*>\s*(.*)\s*<\/p>.*/$2/i) {
		s/(<span.*|)<\/span>\s*//i;
		s/\s+$//;
		if ($_) {
			if ($iscontext) {
			s/<[^>]*>//g;
			if ($iscontext eq '1') {
			    if (!/^\s*PR(É|&#201;)SIDENCE DE /) {
				$bigcontext = $_;
				$subcontext = '';
				$intervention = "<p>$bigcontext</p>";
				print_inter();
			    }
			}else{
				if (!/^\s*(vice-)?pr(é|&#233;)sident/) {
				$subcontext = $_;
				$subcontext =~ s/<[^>]+>//g;
				$intervention = "<p>$subcontext</p>";
				print_inter();
				}
			}
			}elsif(/[a-z]/i){
				s/^\. //;
				$intervention .= "<p>".$_."</p>";
			}
		}
	}
}
print_inter();
