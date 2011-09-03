#!/usr/bin/perl

use HTML::TokeParser;
use URI::Escape;
use Encode;
use utf8;
require "../common/common.pm";


$file = shift;
$yml = shift || 0;

open $fh, $file ;
@content = <$fh>;
$content = "@content";
$content =~ s/\n/ /g;
$content =~ s/  / /g;
@content = ();
seek($fh, 0, 0);
$p = HTML::TokeParser->new($fh);

my %senateur;
my %groupes;

if ($file =~ /%2F([^%]+).html/) {
    $senateur{'id_senat'} = $1;
    $senateur{'url_senat'} = uri_unescape($file);
    $senateur{'url_senat'} =~ s/html\///;
}

$p->get_tag('h1');
$senateur{'Nom'} = $p->get_text('/h1');
utf8::decode($senateur{'Nom'});
$senateur{'Nom'} =~ s/\n/ /;
$senateur{'Nom'} =~ s/\s+$//;
$senateur{'Nom'} =~ s/^\s*//;
$senateur{'Nom'} =~ s/\s+/ /g;
$senateur{'Nom'} =~ s/^([dD]([eEuU] |'))?(.+[A-ZÉË]) ((\s*[A-ZÉ][\L\w][^\s]*)+)$/$4 $1$3/;
$nom = $3;
$nomlc = $nom;
$nomlc =~ s/([A-ZÉ])(\w+ ?)/$1\L$2/g;
$senateur{'Nom'} =~ s/$nom/$nomlc/;
$senateur{'Nom_de_famille'} = $nomlc;
utf8::encode($senateur{'Nom'});
utf8::encode($senateur{'Nom_de_famille'});
$p->get_tag('h2');
$senateur{'Circonscription'} = $p->get_text('/h2', 'br', '/br');
$senateur{'Circonscription'} =~ s/\n/ /g;
$senateur{'Circonscription'} =~ s/.*(ancienn?e? |)s..nat\S+ //i;
$senateur{'Circonscription'} =~ s/^d[eus' ]*(l[a']|) *//;
$senateur{'Circonscription'} =~ s/repr..sentant les //;
$senateur{'Circonscription'} =~ s/ +\(.*//;
$senateur{'Circonscription'} =~ s/\s+puis d.*$//;
$senateur{'Circonscription'} =~ s/^.*les Wallis/Wallis/;
$senateur{'Circonscription'} =~ s/^La //;
$circo = lc($senateur{'Circonscription'});
if ($senateur{'Circonscription'} !~ /(fran.*ais)/i) {
	$senateur{'Circonscription'} =~ s/\s+/-/g;
}

sub groupefonction {
	$str = shift;
	$str =~ s/\n/ /g;
	utf8::decode($str);
	$str =~ s/\//-/g;
	$str =~ s/[,\s]*$//;
	$str =~ s/^\s*//;
	$str =~ s/\s+/ /g;
        $str =~ s/'\s/'/g;
	$str =~ s/membre comité/membre du comité/i;
	$str =~ s/membre commission/membre de la commission/i;
	$str =~ s/de la à la /de la /i;
	$str =~ s/vice président/vice-président/i;
	$str =~ s/\(ancien[^\)]+\)//ig;
	if ($str =~ /^(\S+)\s*,\s*(.*)$/) {
		$str = "$2 / ".lc($1);
	} elsif ($str =~ /^(chargée? )d['une]+ (mission.*$)/i) {
		$str = "$2 / ".lc($1)."de mission";
	} elsif ($str =~ /^(.*) de[ls' ]+((association des )?(voies|maires).*)(( \(|, )président.*)$/i) {
		$str = "$2 / ".lc($1.$5);
        } elsif ($str =~ /^(.*) des (voies.*)(, président.*)$/i) {
                $str = "$2 / ".lc($1.$3);
	} elsif ($str =~ /^(membre|([viceo\s\-]+)?présidente?)( (délégué|titulair|suppléant)e?)? (du |de la |de l')((assemblée|association|délégation|communauté|commission|conseil|comité|gouvernement) .*)$/i) {
		$str = "$6 / ".lc($1.$3);
        } elsif ($str =~ /^(conseiller du président( international)?|membre( du conseil d'aministration)?|adjointe?|administrateur|représentante?) (à la |du |de la |de l' ?|au |des? |d'une |d' ?)(\S.*)$/i) {
                $str = "$5 / ".lc($1);
	} elsif ($str =~ /^(.*) (à la |du |de la |de l'|au |des? |d'une |d')(((conseil|comité|commission|délégation|syndicat|communauté|institut|section|société|agence|association|groupe|pôle) |s[iy].*).*)$/i) {
		$str = "$3 / ".lc($1);
	} elsif ($str =~ /^(.*) (à la |du |de la |de l'|au |des? |d'une |d')((union|assemblée|agglomération|pays) .*)$/i) {
		$str = "$3 / ".lc($1);
	} elsif ($str =~ /^(\S+( \S+)?( [^cg]\S+)?) (à la |du |de la |de l'|au |des? |d'une |d')(\S.*)$/i) {
		$str = "$5 / ".lc($1);
	}
	$str =~ s/\s+(à la|du|de la|de l'|au|de|d'une|d')\s*$//;
	$str =~ s/\s*$//;
	$str =~ s/^\s*//;
	$str =~ s/membre conseil/membre du conseil/i;
	$str =~ s/ \/ pdg/ \/ président directeur général/i;
	utf8::encode($str);
	return ucfirst($str);
}

sub fonctions {
	$autres = shift;
	$old = shift || 0;
	$t = $p->get_tag('ul', 'div');
	if ($t->[0] eq 'div' && $autres && $autres ne "anciengroupe") {
		if ($autres eq "groupes") {
			$fonction = groupefonction($p->get_text('a'));
			$fonction =~ s/^.* \/ //;
			while ($t = $p->get_tag('a', 'br', '/div')) {
		                last if ($t->[0] ne "a" && $t->[0] ne "br");
                		$commission = ucfirst($p->get_text('/a', '/div'));
				$commission =~ s/[\n\s]+/ /g;
                                $commission =~ s/^\s//g;
                                $commission =~ s/\s$//g;
				if ($commission =~ /du groupe/i) {
					$fonction = groupefonction($commission);
					$commission = $fonction;
					$fonction =~ s/^.* \/ //;
					$commission =~ s/ \/ .*$//;
				}
				if (! $groupes{$commission}) {
					$groupes{$commission} = 1;
					$commission .= ' / '.lc($fonction);
					$senateur{'groupes'}{ucfirst($commission)} = 1;
				}
			}
		} else {
			$fonction = groupefonction($p->get_text('/div'));
			$commission = $fonction;
			$commission =~ s/ \/ .*$//;
			if (! $groupes{$commission}) {
				$groupes{$commission} = 1;
				$senateur{$autres}{$fonction} = 1;
			}
		}
		return;
	}
	$limit = "/ul";
	$limit = "/div" if ($autres eq "anciengroupe");
	while ($t = $p->get_tag('li', $limit)) {
		last if ($t->[0] ne "li");
		$commission = $p->get_text('/li', '/a', '/p', $limit);
		last if ($commission =~ /ancien.*nat(eur|rice)/i);
		$commission = groupefonction($commission);
		$commission =~ s/^(S..?nat)/Bureau du $1/;
		$comm = $commission;
		$comm =~ s/ \/ .*$//;
		if (! $groupes{$comm}) {
			if ($autres && $autres ne "anciengroupe") {
				$senateur{$autres}{$commission} = 1;
			} elsif ($commission =~ /nateurs ne figurant sur la liste d'aucun groupe/ || $commission =~ /^groupe /i) {
				$commission =~ s/^groupe (d(u |e l'))?//i;
				$senateur{'groupe'}{$commission} = 1;
				if ($autres && $autres eq "anciengroupe") {
					$groupes{$comm} = 1;
					last;
				}
			} elsif (!$autres) {
				$senateur{'fonctions'}{$commission} = 1;
			}
			$groupes{$comm} = 1;
		}
	}
}

sub mandats {
	$mandatouvert = 0;
	$cause = "";
	$t = $p->get_tag('ul');
	while ($t = $p->get_tag('li', '/ul')) {
		$date1 = $date2 = 0;
		last if ($t->[0] ne "li");
		$election = $p->get_text('/li', '/ul');
		$election =~ s/\n/ /g;
		if ($election =~ /\s+([0-9]*e?r? \S* [0-9]{4})\s+(jusqu')?au\s+([0-9]*e?r? \S* [0-9]{4})/) {
			$date1 = join '/', reverse datize($1);
			$date2 = join '/', reverse datize($3);
		} elsif ($election =~ /\s+([0-9]*e?r? \S* [0-9]{4})/) {
			$date1 = join '/', reverse datize($1);
		}
		$suppleant_de = "";
		$oldcause = $cause;
		$cause = "";
		while ($election =~ /\(([^\)]+\)?)\)/g) {
			$tmpcause = $1;
			if ($tmpcause !~ /(paris|val-d'oise|val-de-marne|$circo)/i) {
				$cause = name_lowerize(lcfirst($tmpcause));
				$cause =~ s/^..?(lue? )/é$1/;
				$cause =~ s/\s+/ /g;
				$cause =~ s/M\.\s*/M. /g;
				$cause =~ s/\.+$//;
				if ($cause =~ /remplacement de M[me\.]+ +([^,]*),/) {
					$suppleant_de = $1;
				}
				last;
			}
		}
		if ($election =~ /Fin de mandat/) {
			if ($oldcause =~ /remplacement de M[me\.]+ ([^,]*),/) {
                                $suppleant_de = $1;
                        }
			$senateur{'fin_mandat'} = $date1;
			$senateur{'premiers_mandats'}{$senateur{'debut_mandat'}." / ".$date1." / ".$cause} = 1;
		} else {
			if ($mandatouvert) {
				$senateur{'premiers_mandats'}{$senateur{'debut_mandat'}." / ".$date1." / ".$oldcause} = 1;
			}
			if ($date2) {
				$mandatouvert = 0;
				$senateur{'premiers_mandats'}{$date1." / ".$date2." / ".$cause} = 1;
			} else {
				$mandatouvert = 1;
				$senateur{'debut_mandat'} = $date1;
			}
		}
	}
	if ($suppleant_de !~ /^$/) {
		$senateur{'suppleant_de'} = $suppleant_de;
	}	
}

if ($content =~ /<h2[^>]*>(Pr..?sidente?) du (S..?nat)/i) {
	$senateur{'fonctions'}{"Bureau du $2 / ".lc($1)} = 1;
}

fonctions();

while($p->get_tag('h2')) {
	$h2 = $p->get_text('/h2');
        if ($h2 =~ /election/i) {
		mandats();
	}elsif ($h2 =~ /mandats|intercommunali|locales/i && $h2 !~ /au cours de ses mandats/) {
                fonctions('autresmandats');
	}elsif ($h2 =~ /autres|interparl/i) {
		fonctions('extras');
	}elsif ($h2 =~ /groupes/i) {
		fonctions('groupes');
	}elsif ($h2 =~ /situation en fin de mandat/i) {
		while ($p->get_tag('ul')) {
			fonctions('anciengroupe');
		}
	}
}


if ($content =~ /place-hemicycle_([0-9]+)/) {
	$senateur{'Place_hemicycle'} = $1;
}

if ($content =~/<dt>Profession<\/dt>[^<]*<dd>([^<]+)<\/dd/) {
	$senateur{'Profession'} = $1;
	$senateur{'Profession'} =~ s/\n/ /;
}

if ($content =~ /N..e? le ([0-9]*e?r? \S* [0-9]*)/) {
	$senateur{'Naissance'} = join '/', reverse datize($1);
}

while($content =~ />([^>\s]+\@[^<\s]+)</g) {
	$senateur{'Mails'}{$1} = 1;
}
delete $senateur{'Mails'}{'notices-senateurs@senat.fr'};
delete $senateur{'Mails'}{'e-bure@u'};

if ($content =~ /Sur Internet :<\/dt>(.*)<dt>/) {
	$sites_str = $1;
	while($sites_str=~ /<a [^>]*href="([^"]+)"/g) {
		$senateur{'Sites_Web'}{$1} = 1;
	}
}

$senateur{'sexe'} = ($content =~ /sentation de M\. /) ? 'H' : 'F';

if ($content =~ /src="([^"]+)"[^>]+Photo de M/i) {
	$senateur{'photo'} = 'http://www.senat.fr'.$1;
}



if ($xml) {
print "<Senateur>\n";

foreach $k (keys %senateur) {
    print '<'.lc($k).'>';
    if (ref($senateur{$k}) eq 'HASH' ) {
	if (ref($senateur{$k}) eq 'HASH') {
	    foreach $h (keys %{$senateur{$k}}) {
		print "\n<hash>";
		foreach $cle (keys (%{$h})) {
		    next unless ($h->{$cle});
		    print "<item key=\"$cle\">";
		    print $h->{$cle};
		    print "</item>";
		}
		print "</hash>";
	    }
	}else{
	    print "\n<item>";
	    print join("</item>\n<item>", @{$senateur{$k}});
	    print "</item>";
	}
    }else {
	print $senateur{$k};
    }
    print '</'.lc($k).'>';
    print "\n";
}
print "</Senateur>\n";
exit;
} 


if ($yml) {
    
    print "senateur_".$senateur{'id_senat'}.":\n"; 
    foreach $k (keys %senateur) { 
	next if ($k =~ /suppléant/i); 
	if (ref($senateur{$k}) =~ /HASH/) { 
	    print "  ".lc($k).":\n"; 
	    foreach $i (keys %{$senateur{$k}}) { 
		print "    - $i\n"; 
	    } 
	}else { 
	    if ($k !~ /suppléant/i) {
		print "  ".lc($k).": ".$senateur{$k}."\n"; 
	    }
	} 
    } 
    print "  type: senateur\n"; 
    
    exit;
}

print "{ ";
foreach $k (keys %senateur) {
    next if ($k =~ /suppléant/i);
    if (ref($senateur{$k}) =~ /HASH/) {
	print '"'.lc($k).'" : [';
	foreach $i (keys %{$senateur{$k}}) {
	    $i =~ s/"//g;
	    print '"'.$i.'",';
	}
	print '"" ], ';
    }else {
	$senateur{$k} =~ s/"//g;
	print '"'.lc($k).'" : "'.$senateur{$k}.'", ';
    }
}
print "\"type\" : \"senateur\" }\n";
