#!/usr/bin/perl

use HTML::TokeParser;
require "finmandats.pm";

$file = shift;
$xml = shift || 0;

open $fh, $file ;
$p = HTML::TokeParser->new($fh);

my %depute;

if ($file =~ /(\d+)/) {
    $depute{'id_an'} = $1;
    $depute{'url_an'} = "http://www.assembleenationale.fr/$legislature/tribun/fiches_id/$1.asp";
    $depute{'Fin_Mandat'} = $fin_mandat{"$1.asp"};
}

sub infosgene {
    $p = shift;
#print $p;
    $t = $p->get_tag("li");
    $depute{'Nom'} = $p->get_text("/li");
    $depute{'Sexe'} = ($depute{'Nom'} =~ /M[ml]/) ? 'F' : 'H';
    $depute{'Nom'} =~ s/^\s*M\S+\s//;
    while($t = $p->get_tag('span', '/div')) {
	return if ($t->[0] eq '/div');
	$txt = $p->get_text('/span');
	$txt =~ /^(\S+)\s*/;
	$e = $1;
	$p->get_tag('td');
	$txt = $p->get_text('/td');
	if ($e =~ /groupe|président/i) {
	    $fonction = ($e =~ /président/i) ? 'président' : 'membre';
	    if ($txt =~ s/apparenté //i) {
		$fonction = 'apparenté';
	    }
	    ${$depute{'Groupe'}}{lc($txt)." / $fonction"} = 1;
	    next;
	}
	next if ($e =~ /Commission/);
	$depute{$e} = $txt;
	return if ($e =~ /Suppléant/);
    }
}


sub contact {
    $p = shift;
    while($p->get_tag('span', '/div')) {
	last if ($t->[0] =~ /^\//);
	$_ = $p->get_text('/span');
	if (/Mél/) {
	    $_ = $p->get_text('/li');
	    if (/MAILTO:([^_]+)_([\w\-]+)/i) {
		${$depute{'Mails'}}{$1.$2.'@assemblee-nationale.fr'} = 1;
	    }
	}elsif (/Site internet/) {
	    $a = $p->get_tag('a');
	    $site = $a->[1]->{'href'};
            $site =~ s/\s+//g;
            $depute{'Site_Web'} = $site;
	}elsif (/Adresses/){
	    while ($t = $p->get_tag('li', '/ul')) {
		last if ($t->[0] =~ /^\//);
		$text = $p->get_text('/li');
		$text =~ s/^\s+//;
		if ($text =~ /\@/) {
	                if ($text =~ /\S+\@/) {
			    while ($text =~ /(\S+@\S+)/g) {
				${$depute{'Mails'}}{$1} = 1;
		 	    }
			}
		} else {
		    ${$depute{'Adresses'}}{$text} = 1
			if ($text);
		}
	    }
	}
    }
}

sub mandat {
    $p = shift;
    while ($t = $p->get_tag('span', '/div')) {
	last if ($t->[0] =~ /^\//);
	$_ = $p->get_text('/span');
	if (/Mandat|Commission|Mission|Office|D.l.gation/) {
	    $text = $p->get_text('ul', '/ul');
	    if ($text =~ /Date de début de mandat : ([\d\/]+) /) {
		$depute{'Debut_Mandat'} = $1;
	    }
	    while ($t = $p->get_tag('li', '/li')) {
		last if ($t->[0] =~ /^\/li/);
		$text = $p->get_text('/li');
		next if ($text =~ /table nominative/i);
		if  ($text =~ /^\(?(\S+\s*\S*\s*(\S\S\S+\s*\S*\s*\S*\)?|))( du | de la | de l')\s*(.*)/) {
		    $fonction = $1;
		    next if ($fonction =~ /Mandat/);
		    $orga = $4;
		    $fonction =~ s/ au nom//;
		    $fonction =~ s/ par les groupes//;
		    $fonction =~ s/ du bureau//;
		    $orga =~ s/\s+$//;
		    $orga =~ s/Assembl�e Nationale/Bureau de l'Assembl�e Nationale/;
		    $orga =~ s/^commission.*\)( du | de la | de l')//i;
		    $deb = "";
		    if ($orga =~ s/( depuis)? le : ([\d\/]+)//) {
			$deb = $2;
		    }
		    $orga =~ s/\s?:.*//;
		    $orga =~ s/(commission des finances) sur.*/\1/i;
                }
		if ($fonction =~ /reprise de l'exercice/i && $deb) {
		    $depute{'Debut_Mandat'} = $deb;
		}elsif ($orga !~ /^\s*$/ && (!($fonctions{lc($orga)}) || $fonctions{lc($orga)} =~ /membre/)) {
                    $fonctions{lc($orga)} = lc($orga)." / ".lc($fonction)." / ".$deb;
		}
		$p->get_tag('/li');
	    }
	}
    }
    foreach $orga (keys %fonctions) {
	${$depute{'Fonctions'}}{$fonctions{$orga}} = 1;
    }
}

sub extra {
    $p = shift;
    while ($t = $p->get_tag('li', 'a')) {
	last if ($t->[0] eq 'a');
	my $text, $fonction, $orga;
	$text = $p->get_text('/li');
	if  ($text =~ /^(\S+\s*\S*\s*\S*)( du | de la | de l')\s*(.*)/) {
	    $fonction = $1;
	    $orga = $3;
	    ${$depute{'Extras'}}{lc($orga)." / ".lc($fonction)} = 1;
	}
    }
}

sub autre_mandat {
    $p = shift;
    my $text;
    while ($t = $p->get_tag('li', 'a')) {
	last if ($t->[0] eq 'a');
	$text = $p->get_text('/li');
	$text =~ s/\n//g;
	$text =~ s/^\s+//;
	$text =~ s/\s+$//;
	$text =~ s/\s\s+/ /g;
	${$depute{'AutresMandats'}}{$text} = 1;
    }    
}

sub place {
    $p = shift;
    $p->get_tag('p');
    $place = $p->get_text('/p');
    if ($place =~ /(\d+)/) {
	$depute{'Place_Hemicycle'} = $1;
    }
}

while($t = $p->get_tag("h2", "img")) {
    if ($t->[0] eq 'img') {
	if (! $depute{'photo'} && $t->[1]{'src'} =~ /photo/) {
	    $img = $t->[1]{'src'};
	    if ($img !~ /^http/) {
		$img = 'http://www.assemblee-nationale.fr'.$img;
	    }
	    $depute{'photo'} = $img;
	}
	next;
    }
    $_ = $p->get_text('/h2');
    if (/Informations générales/) {
	infosgene($p);
    }elsif (/Contacts et site internet/) {
	contact($p);
    }elsif (/Mandats et fonctions à l'Assemblée nationale/ && ! /Anciens/) {
	mandat($p);
    }elsif (/Organismes extra-parlementaires/) {
	extra($p);	
    }elsif (/^Mandats (locaux|intercommunaux)/) {
	autre_mandat($p);
    }elsif (/^Fonctions dans les instances internationales ou judiciaires/) {
	extra($p);
    }elsif (/Place dans l'hémicycle/) {
	place($p);
    }
}

#On récupère le nom de famille à partir des emails
$nomdep = $depute{'Nom'};
$nomdep =~ s/[éèêë]+/e/ig;
@noms = split / /, $nomdep;
if ((join " ", keys %{$depute{'Mails'}}) =~ /(\S+)\@assemblee/) {
    $login = $1;
    while ($login = substr($login, 1)) {
        for($i = 0 ; $i <= $#noms ; $i++) {
	    if ($noms[$i] =~ /$login/i)  {
		if ($nomdep =~ /(\sl[ea]s?\s)?(\S*$login.*$)/i) {
		    $depute{'Nom_de_famille'} = $1.$2;
		    $depute{'Nom_de_famille'} =~ s/[^a-z\s]//gi;
		    last;
		}
	    }
	}
	if ($depute{'Nom_de_famille'}) {
	    last;
	}
    }
}
#Si pas de nom de famille, on le récupère par le nom
if (!$depute{'Nom_de_famille'}) {
    if ($depute{'Nom'} =~ /\S (des? )?(.*)$/i) {
	$depute{'Nom_de_famille'} = $2;
    }
}


if ($xml) {
print "<Depute>\n";

foreach $k (keys %depute) {
    print '<'.lc($k).'>';
    if (ref($depute{$k}) eq 'HASH' ) {
	if (ref(${$depute{$k}}[0]) eq 'HASH') {
	    foreach $h (keys %{$depute{$k}}) {
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
	    print join("</item>\n<item>", @{$depute{$k}});
	    print "</item>";
	}
    }else {
	print $depute{$k};
    }
    print '</'.lc($k).'>';
    print "\n";
}
print "</Depute>\n";
exit;
} 


if ($yml) {
    
    print "  depute_".$depute{'id_an'}.":\n"; 
    foreach $k (keys %depute) { 
	next if ($k =~ /supplÃ©ant/i); 
	if (ref($depute{$k}) =~ /HASH/) { 
	    print "    ".lc($k).":\n"; 
	    foreach $i (keys %{$depute{$k}}) { 
		print "      - $i\n"; 
	    } 
	}else { 
	    if ($k !~ /suppléant/i) {
		print "    ".lc($k).": ".$depute{$k}."\n"; 
	    }
	} 
    } 
    print "    type: depute\n"; 
    
    exit;
}

print "{ ";
foreach $k (keys %depute) {
    next if ($k =~ /suppléant/i);
    if (ref($depute{$k}) =~ /HASH/) {
	print '"'.lc($k).'" : [';
	foreach $i (keys %{$depute{$k}}) {
	    $i =~ s/"//g;
	    print '"'.$i.'",';
	}
	print '"" ], ';
    }else {
	$depute{$k} =~ s/"//g;
	print '"'.lc($k).'" : "'.$depute{$k}.'", ';
    }
}
print "\"type\" : \"depute\" }\n";
