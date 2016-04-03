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
$content =~ s/&nbsp;/ /ig;
$content =~ s/[\r\n\s  ]+/ /ig;
$p = HTML::TokeParser->new(\$content);

my %senateur;
my %groupes;

if ($file =~ /%2F([^%]+).html/) {
    $senateur{'id_institution'} = $1;
    $senateur{'url_institution'} = uri_unescape($file);
    $senateur{'url_institution'} =~ s/html\///;
}

$p->get_tag('h1');
$senateur{'Nom'} = $p->get_text('/h1');
utf8::decode($senateur{'Nom'});
$senateur{'Nom'} =~ s/^M(\.|me|lle) //g;
$senateur{'Nom'} =~ s/\n/ /;
$senateur{'Nom'} =~ s/\s+$//;
$senateur{'Nom'} =~ s/^\s*//;
$senateur{'Nom'} =~ s/\s+/ /g;
$senateur{'Nom_de_famille'} = $senateur{'Nom'};
if ($senateur{'Nom'} =~ /^(de |d'|du )?[A-ZÉËÈÏÙ]{2}/) {
	$senateur{'Nom'} =~ s/^([dD]([eEuU] |'))?(.+[A-ZÉË]) ((\s*[A-ZÉ][\L\w][^\s]*)+)$/$4 $1$3/;
	#print STDERR $senateur{'Nom'}."\n";
	$nom = $3;
	$nomlc = $nom;
	$nomlc =~ s/([A-ZÉ])(\w+ ?)/$1\L$2/g;
	$senateur{'Nom'} =~ s/$nom/$nomlc/;
	$senateur{'Nom_de_famille'} = $nomlc;
}else{
	$nomlc = $senateur{'id_institution'};
	$nomlc =~ s/_.*//;
	$nomlc =~ s/[aeiou]/./gi; #remove accents
	$senateur{'Nom_de_famille'} =~ s/.* ($nomlc)/$1/i;
}
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
	$str =~ s/"//g;
	$str =~ s/(['\(])\s/$1/g;
	$str =~ s/¿/'/g;
	$str =~ s/membre comité/membre du comité/i;
	$str =~ s/admistration/administration/ig;
	$str =~ s/membre commission/membre de la commission/i;
	$str =~ s/de la (d'|à la )/de la /i;
	$str =~ s/vice président/vice-président/i;
	$str =~ s/ ([^\s]*-métropole) (communauté.*)$/ la $2 de $1/i;
	$str =~ s/\(ancien[^\)]+\)//ig;
	if ($str =~ /^(\S+)\s*,\s*(.*)$/) {
		$str = "$2 / ".lc($1);
	} elsif ($str =~ /^(chargée? )d['une]+ (mission.*$)/i) {
		$str = "$2 / ".lc($1)."de mission";
	} elsif ($str =~ /^(.*) de[ls' ]+((association des )?(voies|maires).*)(( \(|, )président.*)$/i) {
		$str = "$2 / ".lc($1.$5);
	} elsif ($str =~ /^(.*) au sein (du |de la |de l')(.*)$/i) {
		$str = "$3 / ".lc($1);
	} elsif ($str =~ /^(.*) des (voies.*)(, président.*)$/i) {
		$str = "$2 / ".lc($1.$3);
	} elsif ($str =~ /^([^(]* (maire|conseil d'administration|conseil (génér|région|territori)al|communauté urbaine|communauté de communes|communauté d(e l)?'agglomération)) (du |de la |de l'|des |de |d')?(.+)$/i) {
		$str = "$6 / ".lc($1);
	} elsif ($str =~ /^(membre|([1-eèrpmvico\s\-]+)?présidente?|secrétaire)( (d'honneur|du bureau|délégué|titulair|suppléant)e?)? (du |de la |de l')((assemblée|section|association|délégation|communauté|commission|conseil|société|syndicat|comité|gouvernement) .*)$/i) {
		$str = "$6 / ".lc($1.$3);
	} elsif ($str =~ /^(conseiller du président( international)?|membre( du conseil d'aministration)?|adjointe?|administrateur|représentante?) (à la |du |de la |de l' ?|au |des? |d'une |d' ?)(\S.*)$/i) {
		$str = "$5 / ".lc($1);
        } elsif ($str =~ /^(.*) (à la |du |de la |de l'|au |des? |d'une |d')(((délégation|syndicat|communauté|institut|section|société|agence|association|pôle) |s[iy].*).*)$/i) {
		$str = "$3 / ".lc($1);
	} elsif ($str =~ /^(.*) (à la |du |de la |de l'|au |des? |d'une |d')(((conseil|comité|commission|groupe) |s[iy].*).*)$/i) {
		$str = "$3 / ".lc($1);
	} elsif ($str =~ /^(.*) (à la |du |de la |de l'|au |des? |d'une |d')((union|assemblée|agglomération|pays) .*)$/i) {
		$str = "$3 / ".lc($1);
	} elsif ($str =~ /^(\S+( \S+)?( [^cg]\S+)?) (à la |du |de la |de l'|au |des? |d'une |d')(\S.*)$/i) {
		$str = "$5 / ".lc($1);
	}
	$str =~ s/\s+(à la|du|de la|de l'|au|de|d'une|d')\s*$//;
	$str =~ s/\s*$//;
	$str =~ s/^\s*//;
	$str =~ s/^é/É/;
	$str =~ s/membre conseil/membre du conseil/i;
	$str =~ s/ \/ pdg/ \/ président directeur général/i;
	$str =~ s/^(Assemblée parlementaire de la franco)/Section française de l'$1/i;
	$str =~ s/^(Assemblée parlementaire de l'OTAN)/Délégation française à l'$1/i;
	$str =~ s/Groupe Groupe/Groupe/i;
    $str =~ s/écologiste/Écologiste/;
    $str =~ s/^(.*) \((pour .*)\) \/ (.*)$/$1 \/ $3 $2/;
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
				if ($commission && ! $groupes{$commission}) {
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
		$commission = $p->get_text('/li', '/p', $limit);
        #print STDERR "COMMISSION ".$commission."\n";
        next if ($commission =~ /jusqu'au 30 septembre 2014/i);
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
				$commission =~ s/groupe \/ /groupe politique \/ /i;
				$commission =~ s/centriste - UDF/Centriste\//i;
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

sub fltdate {
    $_y = $_m = shift;
    $_y =~ s/^.*\/(\d+)$/\1/;
    $_m =~ s/^.*\/(\d+)\/.*$/\1/;
    return $_y + $_m/12;
}

sub distantdates {
    return (abs(fltdate(shift) - fltdate(shift)) > 0.3);
}

sub add_mandat {
    $_deb = shift;
    $_end = shift;
    $_cau = shift;
    $senateur{'premiers_mandats'}{$_deb." / ".$_end." / ".$_cau} = 1;
    if (distantdates($lastend,$_deb)) {
        $fakedebut = $_deb;
    }
    $lastend = $_end;
    $mandatouvert = 0;
}

sub mandats {
	$mandatouvert = 0;
	$cause = "";
    $fakedebut = "";
    $lastend = "";
	$t = $p->get_tag('ul');
	while ($t = $p->get_tag('li', '/ul')) {
		last if ($t->[0] ne "li");
		$date1 = $date2 = 0;
		$election = $p->get_text('/li', '/ul');
		$election =~ s/\n/ /g;
		if ($election =~ /\s+([0-9]*e?r? \S* [0-9]{4})\s+(jusqu')?au\s+([0-9]*e?r? \S* [0-9]{4})(.*en cours|)/) {
			$date1 = join '/', reverse datize($1);
			$date2 = join '/', reverse datize($3) unless ($4);
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
				utf8::decode($cause);
				$cause =~ s/^..?(lue? )/é$1/;
				$cause =~ s/\s+/ /g;
				$cause =~ s/M\.\s*/M. /g;
				$cause =~ s/\.+$//;
				utf8::encode($cause);
				if ($cause =~ /remplacement de M[me\.]+ +([^,]*),/) {
					$suppleant_de = $1;
				}
				last;
			}
		}
		if ($election =~ /Fin de mandat/ || $election =~ /D..mission /) {
			if ($oldcause =~ /remplacement de M[me\.]+ ([^,]*),/) {
                                $suppleant_de = $1;
                        }
			$senateur{'fin_mandat'} = $date1;
            add_mandat($senateur{'debut_mandat'}, $date1, $cause);
		} else {
			if ($mandatouvert) {
                add_mandat($senateur{'debut_mandat'}, $date1, $oldcause);
			}
			if ($date2) {
                add_mandat($date1, $date2, $cause);
			} else {
                if (!$senateur{'debut_mandat'}) {
                    $fakedebut = $date1;
                }
                $senateur{'debut_mandat'} = $date1;
				$mandatouvert = 1;
			}
		}
	}
	if ($date1 && !$senateur{'debut_mandat'}) {
		$senateur{'debut_mandat'} = $date1;
	}
	if ($date2) {
		$senateur{'fin_mandat'} = $date2;
	}
    if ($mandatouvert) {
        add_mandat($senateur{'debut_mandat'}, "", $cause);
    }
    $senateur{'debut_mandat'} = $fakedebut;
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
	}elsif ($h2 =~ /autres/i) {
		fonctions('extras');
	}elsif ($h2 =~ /groupes/i) {
		fonctions('groupes');
	}elsif ($h2 =~ /en fin de mandat/i) {
		fonctions('anciengroupe');
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
	while($sites_str=~ /<a [^>]*href=" *([^" ][^"]+)"/g) {
        $site = $1;
        $site =~ s/^.*twitter.com\/[!#\/]*([a-z\d_]+).*$/https:\/\/twitter.com\/\1/i;
		$senateur{'Sites_Web'}{$site} = 1;
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

    print "senateur_".$senateur{'id_institution'}.":\n";
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
