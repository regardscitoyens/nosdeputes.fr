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
if ($senateur{'Circonscription'} !~ /(fran.*ais)/i) {
	$senateur{'Circonscription'} =~ s/\s+/-/g;
}
$senateur{'Circonscription'} =~ s/^.*les Wallis/Wallis/;
$senateur{'Circonscription'} =~ s/^La //;

sub groupefonction {
	$str = shift;
	$str =~ s/\n/ /g;
	$str =~ s/\s+$//;
	$str =~ s/^\s*//;
	$str =~ s/\s+/ /g;
	if ($str =~ /^(membre) (à la |du |de la |de l'|au |de |d'une |d')(\S.*)$/i) {
		return ucfirst($3).' / '.lc($1);
	} elsif ($str =~ /^(\S+( \S+)?( [^cg]\S+)?) (à la |du |de la |de l'|au |de |d'une |d')(\S.*)$/i) {
		return ucfirst($5).' / '.lc($1);
	}
	$str =~ s/(à la|du|de la|de l'|au|de|d'une|d')$//;
	return ucfirst($str);
}

sub fonctions {
	$autres = shift;
	$old = shift || 0;
	$t = $p->get_tag('ul', 'div');
	if ($t->[0] eq 'div' && $autres && $autres ne "anciengroupe") {
		if ($autres eq "groupes") {
			$fonction = lc(groupefonction($p->get_text('a')));
			while ($t = $p->get_tag('a', '/div')) {
		                last if ($t->[0] ne "a");
                		$commission = ucfirst($p->get_text('/a', '/div'));
				$commission =~ s/\n/ /g;
				if (! $groupes{$commission}) {
					$groupes{$commission} = 1;
					$commission .= ' / '.$fonction;
					$senateur{$autres}{ucfirst($commission)} = 1;
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
	while ($t = $p->get_tag('li', '/ul')) {
		last if ($t->[0] ne "li" );
		$commission = $p->get_text('/li', '/a', '/ul');
		last if ($commission =~ /ancien.*nat(eur|rice)/i);
		$commission = groupefonction($commission);
		$commission =~ s/^(S..?nat)/Bureau du $1/;
		$comm = $commission;
		$comm =~ s/ \/ .*$//;
		if (! $groupes{$comm}) {
			if ($autres && $autres ne "anciengroupe") {
				$senateur{$autres}{$commission} = 1;
			} elsif ($commission =~ /nateurs ne figurant sur la liste d'aucun groupe/ || $commission =~ /groupe /i) {
				$commission =~ s/groupe (d(u |e l'))?//i;
				$senateur{'groupe'}{$commission} = 1;
				if ($autres && $autres eq "anciengroupe") {
					$groupes{$comm} = 1;
					last;
				}
			} elsif ($autres ne "anciengroupe") {
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
		if ($election =~ /\((.*)\)/) {
			$cause = name_lowerize(lcfirst($1));
			if ($cause =~ /remplacement de M[me\.]+ ([^,]*),/) {
				$suppleant_de = $1;
			}
		} else {
			$cause = "";
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
	}elsif ($h2 =~ /autres|interparl/i) {
		fonctions('extras');
	}elsif ($h2 =~ /groupes/i) {
		fonctions('groupes');
	}elsif ($h2 =~ /mandats|intercommunali/i && $h2 !~ /au cours de ses mandats/) {
		fonctions('autresmandats');
	}elsif ($h2 =~ /situation en fin de mandat/i) {
		fonctions('anciengroupe');
		fonctions('anciengroupe');
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
