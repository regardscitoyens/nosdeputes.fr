#!/usr/bin/perl

use HTML::TokeParser;
use URI::Escape;
use utf8;

$file = shift;
$xml = shift || 0;

open $fh, $file ;
@content = <$fh>;
$content = "@content";
$content =~ s/\n/ /g;
$content =~ s/  / /g;
@content = ();
seek($fh, 0, 0);
$p = HTML::TokeParser->new($fh);

sub datize {
	$date = shift;
	%mois = ('janvier'=>'01', 'fvrier'=>'02', 'mars'=>'03', 'avril'=>'04', 'mai'=>'05', 'juin'=>'06', 'juillet'=>'07','aot'=>'08', 'septembre'=>'09', 'octobre'=>'10', 'novembre'=>'11', 'dcembre'=>'12');
	$date =~ /([0-9]*) (\S*) ([0-9]*)/;
	$jour = $1;
	$mois = $2;
	$annee = $3;
	$mois =~ s/[^a-z].//;
	return sprintf("%02d/%02d/%04d", $jour,$mois{$mois},$annee);
}

my %senateur;

if ($file =~ /%2F([^%]+).html/) {
    $senateur{'id_senat'} = $1;
    $senateur{'url_senat'} = uri_unescape($file);
    $senateur{'url_senat'} =~ s/html\///;
}
$p->get_tag('h1');
$senateur{'Nom'} = $p->get_text('/h1');
$senateur{'Nom'} =~ s/^\n?(.+) ([A-ZÉ][^A-ZÉ].*)$/$2 $1/;
$senateur{'Nom_de_famille'} = $1;
$p->get_tag('h2');
$senateur{'Circonscription'} = $p->get_text('/h2');
$senateur{'Circonscription'} =~  s/\n/ /g;
$senateur{'Circonscription'} =~ s/.*(ancienn?e? |)s..nat\S+ //i;
$senateur{'Circonscription'} =~ s/^d[eus' ]*(l[a']|) *//;
$senateur{'Circonscription'} =~ s/repr..sentant les //;
$senateur{'Circonscription'} =~  s/ +\(.*//;

sub groupefonction {
	$str = shift;
	$str =~ s/\n/ /g;
	if ($str =~ /^\s*(\S+) (du|de la|de l'|au|d'une) *(\S.*)$/) {
		return $3.' / '.$1;
	}
	return $str;
}

sub fonctions {
	$autres = shift;
	$t = $p->get_tag('ul', 'div');
	if ($t->[0] eq 'div' && $autres) {
		$fonction = groupefonction($p->get_text('/div'));
		$senateur{$autres}{$fonction} = 1;
		return;
	}
	while ($t = $p->get_tag('li', '/ul')) {
		last if ($t->[0] ne "li");
		$commission = $p->get_text('/li', '/ul');
		$commission = groupefonction($commission);
		if ($autres) {
			$senateur{$autres}{$commission} = 1;
		}elsif ($commission =~ s/groupe //) {
			$senateur{'groupe'}{$commission} = 1;
		} else {
			$senateur{'fonctions'}{$commission} = 1;
		}
	}
}

fonctions();
while($p->get_tag('h2')) {
	$h2 = $p->get_text('/h2');
	if ($h2 =~ /groupes|autres/i) {
		fonctions('extras');
	}elsif ($h2 =~ /mandats/i) {
		fonctions('autresmandats');
	}
}


if ($content =~ /place-hemicycle_([0-9]+)/) {
	$senateur{'Place_hemicycle'} = $1;
}

if ($content =~/<dt>Profession<\/dt>[^<]*<dd>([^<]+)<\/dd/) {
	$senateur{'Profession'} = $1;
	$senateur{'Profession'} =~ s/\n/ /;
}

if ($content =~ /N..e? le ([0-9]* \S* [0-9]*)/) {
	$senateur{'Naissance'} = datize($1);
}

%mails = ();
while($content =~ />([^>\s]+\@[^<\s]+)</g) {
	$senateur{'Mails'}{$1} = 1;
}
delete $senateur{'Mails'}{'notices-senateurs@senat.fr'};
delete $senateur{'Mails'}{'e-bure@u'};

$senateur{'sexe'} = ($content =~ /sentation de M\. /) ? 'H' : 'F';
if ($content =~ /Elue? le ([0-9]* \S* [0-9]{4})/) {
	$senateur{'debut_mandat'} = datize($1);
}
if ($content =~ /Fin de mandat le ([0-9]* \S* [0-9]{4})/) {
	$senateur{'fin_mandat'} = datize($1);
}
if ($content =~ /src="([^"]+)"[^>]+Photo de M/) {
	$senateur{'photo'} = 'http://www.senat.fr'.$1;
}
if ($xml) {
print "<Senateur>\n";

foreach $k (keys %senateur) {
    print '<'.lc($k).'>';
    if (ref($senateur{$k}) eq 'HASH' ) {
	if (ref(${$senateur{$k}}[0]) eq 'HASH') {
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
    
    print "  senateur_".$senateur{'id_an'}.":\n"; 
    foreach $k (keys %senateur) { 
	next if ($k =~ /suppléant/i); 
	if (ref($senateur{$k}) =~ /HASH/) { 
	    print "    ".lc($k).":\n"; 
	    foreach $i (keys %{$senateur{$k}}) { 
		print "      - $i\n"; 
	    } 
	}else { 
	    if ($k !~ /suppléant/i) {
		print "    ".lc($k).": ".$senateur{$k}."\n"; 
	    }
	} 
    } 
    print "    type: senateur\n"; 
    
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
