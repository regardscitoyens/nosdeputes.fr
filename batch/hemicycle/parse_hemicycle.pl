#!/usr/bin/perl

use URI::Escape;
use HTML::Entities;
use Encode;
require "../common/common.pm";
use utf8;

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
%num_lois = ();

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
$doc =~ s/&nbsp;/ /g;
$doc =~ s/<\/i> *<i>/ /gi;
$doc =~ s/(info_entre_parentheses">[^<]*)<\/span> *<i>([^<]*)<\/i>/$1 $2<\/span>/gi;

#$doc =~ s/<\/?(i|cri|div)[^>]*>//ig;
#$doc =~ s/<p[^>]+class="[^"]+_article"[^>]*>[^<]+<\/p>//g;


$doc =~ s/  +/ /g;
$doc =~ s/<\/p>/<\/p>\n/g;

$intervention = '';
$timestamp = 0;
sub print_inter {
	if ($heure && $intervention && $intervention ne "<p></p>") {
		$intervention =~ s/\s*,\s*/, /g;
		$timestamp += 20;
		$context = $bigcontext;
		$context =~ s/ suite$//;
		$context .= ' > '.$subcontext if ($subcontext);
		if ($resetcontexte) {
			if (!$inter) {
				$context = "";
			} else {
				$resetcontexte = 0;
                        	if ($intervention !~ /Nous poursuivons / && $intervention !~ /séance.*reprise.*(amendement.*présenté|(poursuiv|continu|repren)ons.*(discussion|examen|débat)|dans.*discussion.*sommes.*(arrivés|parvenus)|parole.*répondre.*orateurs|je.*mets.*aux.*voix|nous.*allons.*procéder.*(scrutin|délibération))/) {
					$oldbigcontext = $bigcontext;
                                	$bigcontext = "";
                                	$subcontext = "";
					$context = "";
					$numeros_loi = '';
				}
                        }       
                }

		$cpt = 0;
		if ($context =~ /procès verbal|ordre du jour|Conf[&#\d;é]+rence des pr[&#\d;é]+sidents|question.*(crible|orale|gouvernement)/i) {
			$numeros_loi = '';
		}elsif ($subcontext !~ /article|discussion g/i && $intervention =~ /((projet|proposition|motion|lettre)\s[^<]*(n°|n<sup>os?<\/sup>|nos?|n&[^;]+;&[^;]+;)[^<\.]{1,5}\d[^<\.]+)/i && $intervention !~ /amendements? n/) {
			while ($intervention =~ /((projet|proposition|motion|lettre)\s[^<]*(n°|n<sup>os?<\/sup>|nos?|n&[^;]+;&[^;]+;)[^<\.]{1,5}\d[^<\.]+)/gi) {
                          $docs = $1;
			  $docs =~ s/°//g;
                          $docs =~ s/&[^;]*;//g;
			  if ($docs =~ /(\d+)([\(\[\, ]+(\d{4}[- ]+\d{4})|)/) {
                          while ($docs =~ /\D(\d{1,3})([\(\[\, ]+(\d{4}[- ]+\d{4})|)/g) {
                                 if ($3) {
                                          $numeros_loi .= law_numberize($1,$3).",";
                                 }else{
                                          $numeros_loi .= law_numberize($1,$session).",";
                                 }
                          }
			  }
			  }
                          chop($numeros_loi) if ($cpt);
			  $num_lois{$bigcontext} = $numeros_loi if ($cpt);
                }
		$numeros_loi = $num_lois{$bigcontext} if (!$numeros_loi);
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
		$secondinter =~ s/^\s*M[mles]{0,3}[\.\s]+//;
		$json  = '{"contexte": "'.quotize($context).'", "intervention": "'.quotize($intervention).'", "timestamp": "'.$timestamp.'", "date": "'.$date.'", "source": "'.$url_source.$source.'", "heure":"'.$heure.'", "intervenant": "'.name_lowerize($inter,1).'", "fonction": "'.$fonction.'", "intervenant_url": "'.$url_inter.'", "session":"'.$session.'"';
		$json .= ', "numeros_loi":"'.$numeros_loi.'"' if ($numeros_loi && $context);
		$json .= ', "amendements":"'.$amendements.'"' if ($amendements && $context);
		$json .= "}\n";
		utf8::encode($json);
		print $json;
		if ($secondinter) {
		$json  = '{"contexte": "'.quotize($context).'", "intervention": "'.quotize($intervention).'", "timestamp": "'.$timestamp.'", "date": "'.$date.'", "source": "'.$url_source.$source.'", "heure":"'.$heure.'", "intervenant": "'.name_lowerize($secondinter,1).'", "fonction": "", "intervenant_url": "'.$url_inter.'", "session":"'.$session.'"';
                $json .= ', "numeros_loi":"'.$numeros_loi.'"' if ($numeros_loi && $context);
                $json .= ', "amendements":"'.$amendements.'"' if ($amendements && $context);
                $json .= "}\n";
		utf8::encode($json);
                print $json;
		}
	}
	$intervention = '';
	$inter = '';
	$fonction = '';
	$url_inter = '';
	$amendements = '';
}

$doc =~ s/(class="titre_S1"[^>]*>[^<]*)\s*<[^\n]*\n[^\n]*class="titre_S1"[^>]*>\s*/\1 /g;
$resetcontexte = $oldhtab = 0;
foreach (split /\n/, $doc) {
    s/&(nbsp|#160);/ /ig;
    utf8::decode($_);
    s/ n<sup>[0os\s]+<\/sup>\s*/ n° /ig;
    $_ = decode_entities($_);
    if (/<\/span><span([^>]*>)/ && $1 !~ /orateur_qualite/) {
	s/<\/span><span[^>]*>/ /g;
	s/ ' /'/g;
    }
	if (/ (id|name)="([^"]+)"/) {
		$source = "#$2";
	}
        if (/<(i|span class="info_entre_parentheses")>\s*\((.*)<\/(i|span)>([\.\s\)]*)/) {
                $didasc = $2;
		$didasc =~ s/\)$//;
		$didasc =~ s/<[^>]*>//g;
		$didasc =~ s/vingt et une/vingt-et-une/gi;
                if ($didasc =~ /(ouverte|reprise) (&#224;|à) (midi\s*\S*|\S+ heures\s*\S*)\W/) {
                        $h = heurize($3);
			($htab) = split /:/, $h;
			if (!$heure || ($htab > 13 && $oldhtab < 14) || ($htab > 20 && $oldhtab < 21)) {
                            print_inter();
			    $intervention = "<p>$didasc</p>";
			    $oldhtab = $htab;
			    if (!$heure) {
				$heure = $h;
				print_inter();
				next;
			    }
			    print_inter();
                            $resetcontexte = 1 if ($heure);
                            $heure = $h;
			    $timestamp = 0;
			}
                }
        }
	if (/>[^a-z]*Pr(é|É)sidence de (M[^<]*)/i) {
		$president = $2;
		$president =~ s/^\s*M[mles]{0,3}[\.\s]+//;
		$president =~ s/\s([a-z])(\w+)$/ \U$1$2/;
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
		$tmpinter =~ s/^\s*M[mles]{0,3}[\.\s]+//;
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
		$tmpfonction =~ s/^[,\s]+//;
		if ($tmpinter ne $inter) {
			print_inter();
			$inter = $tmpinter;
			$url_inter = $tmpurl_inter;
			$fonction = $tmpfonction;
		}
		s/<span class="info_entre_parentheses">([^\(][^<]*)<\/span>/<i>\1<\/i>/g;
	}
	s/<\/sup><i>/<\/sup> <i>/gi;
	if (!(/"titre_S([123][^"]*)"/ || /"mention_(article)"/)) {
	    s/<\/?(sup|br?|strong)\/?>//gi;
	    while (s/([^>]*)<(i|span class="info_entre_parentheses")>\(([^\)]*)\)?<\/(i|span)>([\.\s\)]*)//) {
		$i = $1;
		$didasc = $3;
		$i =~ s/<[^>]*>//g;
		$i =~ s/\s+/ /g;
		$i =~ s/\s+$//;
		$intervention .= "<p>".$i."</p>";
		$didasc =~ s/<[^>]*>//gi;
		$didasc =~ s/\)//g;
		if ($didasc && $didasc !~ /^(suite|nouveau)$/i) {
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
	    }
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
		s/<(i|span class="info_entre_parentheses")>\([^\)]*\)?<\/(i|span)>//;
	}
	if (s/.*id="(intv_|)par_[^>]*>\s*(.*)\s*<\/p>.*/$2/i) {
		s/(<span.*|)<\/span>\s*//i;
		s/\.\.\.\.+//g;
		s/\s+$//;
		if ($_) {
			if ($iscontext) {
			s/<[^>]*>//g;
			if ($iscontext eq '1') {
			    if (!/^\s*PR(É|&#201;)SIDENCE DE /) {
				$resetcontexte = 0;
				$bigcontext = $_;
				$subcontext = '';
				$intervention = "<p>$bigcontext</p>";
				print_inter();
			    }
			}else{
				if (!/^\s*(vice-)?pr(é|&#233;)sident/) {
				$resetcontexte = 0;
				$bigcontext = $oldbigcontext if (!$bigcontext);
				$subcontext = $_;
				$subcontext =~ s/<[^>]+>//g;
				$subcontext =~ s/\s+/ /g;
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
