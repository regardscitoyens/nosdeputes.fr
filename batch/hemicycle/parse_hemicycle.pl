#!/usr/bin/perl

open(FILE, shift);
@doc = <FILE>;
$doc = "@doc";
@doc = ();
close FILE;

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

$heure{'neuf'} = '09';
$heure{'dix'} = '10';
$heure{'onze'} = '11';
$heure{'douze'} = '12';
$heure{'treize'} = '13';
$heure{'quatorze'} = '14';
$heure{'quinze'} = '15';
$heure{'seize'} = '16';
$heure{'dix-sept'} = '17';
$heure{'dix-huit'} = '18';
$heure{'dix-neuf'} = '19';
$heure{'vingt'} = '20';
$heure{'vingt et une'} = '21';
$heure{'vingt-deux'} = '22';
$heure{'quarante'} = '45';
$heure{'quarante-cinq'} = '45';
$heure{'trente'} = '30';
$heure{'trente-cinq'} = '35';
$heure{'quinze'} = '15';
$heure{'zéro'} = '00';
$heure{'cinq'} = '00';
$heure{''} = '00';


if ($doc =~ /ance du (\d+)e?r? (\S+) (\d+)/i) {
	$date = sprintf("%04d-%s-%02d", $3, $mois{$2}, $1);
}

$doc =~ s/\n/ /g;
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
	if ($heure && $intervention) {
		$timestamp += 20;
		$context = $bigcontext;
		$context .= ' > '.$subcontext if ($subcontext);
	print '{"contexte": "'.$context.'", "intervention": "'.$intervention.'", "timestamp": "'.$timestamp.'", "date": "'.$date.'", "source": "'.$source.'", "heure":"'.$heure.'", "intervenant": "'.$inter.'", "fonction": "'.$fonction.'", "intervenant_url": "'.$url_inter.'"}'."\n";
	}
	$intervention = '';
	$inter = '';
	$fonction = '';
	$url_inter = '';
}

foreach (split /\n/, $doc) {
	if (/name="([^"]+)"/) {
		$source = "#$1";
	}
        if (s/<span class="info_entre_parentheses">\((.*)\)\S*<\/span>//) {
                $didasc = $1;
                $didasc =~ s/<[^>]*>//gi;
		print_inter() if ($heure || $intervenant);
                if ($didasc =~ /(ouverte|reprise) &#224; (\S+) heures\s*(\S*)\W/) {
                        $heure = sprintf("%02d:%02d", $heure{$2}, $heure{$3});
			$timestamp = 0;
                }
		$intervention .= '<p>'.$didasc.'</p>';
		
        }
	if (/Pr\&\#233\;sidence de (M[^<]*)/) {
		$president = $1;
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
		if ($tmpinter =~ /Mm?e?\.? l[ae] (pr\&\#233\;sidente?)/ && $president) {
			$tmpinter = $president;
			$tmpfonction = $1;
		}elsif (/class="orateur_qualite"[^>]*>([^>]*)</) {
			$tmpfonction = $1;
		}
		if (/href="(\/sen[^"]+)"/i) {
			$tmpurl_inter = "http://www.senat.fr$1";
		}
		$tmpinter =~ s/<[^>]*>//g;
		$tmpinter =~ s/[\.,]\s*$//;
		if ($tmpinter ne $inter) {
			print_inter();
			$inter = $tmpinter;
			$url_inter = $tmpurl_inter;
			$fonction = $tmpfonction;
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
	}
	if (s/.*id="(intv_|)par_[^>]*>\s*(.*)\s*<\/p>.*/$2/i) {
		s/(<span.*|)<\/span>\s*//i;
		s/\s+$//;
		if ($_) {
			if ($iscontext) {
			if ($iscontext eq '1') {
				$bigcontext = $_;
				$subcontext = '';
			}else{
				$subcontext = $_;
			}
			}else{
				$intervention .= "<p>$_</p>";
			}
		}
	}
}
