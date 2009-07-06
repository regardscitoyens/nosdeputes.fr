#!/usr/bin/perl

use HTML::TokeParser;

$file = shift;

open $fh, $file ;
$p = HTML::TokeParser->new($fh);

my %depute;

if ($file =~ /(\d+)/) {
    $depute{'id'} = $1;
    $depute{'url'} = "http://www.assembleenationale.fr/13/tribun/fiches_id/$1.asp";
}

sub infosgene {
    $p = shift;
    $t = $p->get_tag("li");
    $depute{'Nom'} = $p->get_text("/li");
    $depute{'Nom'} =~ s/^\s*M\S+\s//;
    $depute{'Sexe'} = ($depute{'nom'} =~ /Mm/) ? 'F' : 'M'; 
    while($t = $p->get_tag('u', '/div')) {
	return if ($t->[0] eq '/div');
	$txt = $p->get_text('/u');
	$txt =~ /^(\S+)\s*/;
	$e = $1;
	next if ($e =~ /Commission/);
	$p->get_tag('td');
	$depute{$e} = $p->get_text('/td');
	return if ($e =~ /Suppléant/);
    }
}


sub contact {
    $p = shift;
    while($p->get_tag('u', '/div')) {
	last if ($t->[0] =~ /^\//);
	$_ = $p->get_text('/u');
	if (/Mél/) {
	    $_ = $p->get_text('/li');
	    if (/MAILTO:([^_]+)_(\w+)/) {
		push @{$depute{'Mails'}}, $1.$2.'@assemblee-nationale.fr';
	    }
	}elsif (/Site internet/) {
	    $a = $p->get_tag('a');
	    $depute{'Web'} = $a->[1]->{'href'};
	}elsif (/Adresses/){
	    while ($t = $p->get_tag('li', '/ul')) {
		last if ($t->[0] =~ /^\//);
		$text = $p->get_text('/li');
		$text =~ s/^\s+//;
		if ($text =~ /^\S+\@/) {
		    push @{$depute{'Mails'}}, $text;
		}else {
		    push @{$depute{'Adresses'}}, $text;		    
		}
	    }
	}
    }
}

sub mandat {
    $p = shift;
    while ($t = $p->get_tag('u', '/div')) {
	last if ($t->[0] =~ /^\//);
	$_ = $p->get_text('/u');
	if (/Mandat|Commission|Délégation|Mission/) {
	    $text = $p->get_text('ul');
	    if ($text =~ /Date de début de mandat : ([\d\/]+) /) {
		$depute{'DebutMandat'} = $1;
	    }
	    while ($t = $p->get_tag('li', '/li')) {
		last if ($t->[0] =~ /^\//);
		$text = $p->get_text('/li');
		if  ($text =~ /^(\S+\s*\S*)( du | de la | de l')\s*(.*)/) {
		    $fonction = $1;
		    $orga = $3;
		    $deb = "";
		    if ($orga =~ s/ depuis le : ([\d\/]+)//) {
			$deb = $1;
		    }
		}
		push @{$depute{'Fonctions'}}, {'orga' => lc($orga), 'fonction' =>lc($fonction), 'deb'=>$deb};
		$p->get_tag('/li');
	    }
	}
    }
}

sub extra {
    $p = shift;
    while ($t = $p->get_tag('li', 'a')) {
	last if ($t->[0] eq 'a');
	my $text, $fonction, $orga;
	$text = $p->get_text('/li');
	if  ($text =~ /^(\S+\s*\S*)( du | de la | de l')\s*(.*)/) {
	    $fonction = $1;
	    $orga = $3;
	}
	push @{$depute{'Extras'}}, {'orga' => lc($orga), 'fonction' =>lc($fonction)};
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
	push @{$depute{'AutresMandats'}}, $text;
    }    
}

sub place {
    $p = shift;
    $p->get_tag('p');
    $place = $p->get_text('/p');
    if ($place =~ /(\d+)/) {
	$depute{'Place'} = $1;
    }
}

while($p->get_tag("h1")) {
    $_ = $p->get_text('/h1');
    if (/Informations générales/) {
	infosgene($p);
    }elsif (/Contacts et site internet/) {
	contact($p);
    }elsif (/Mandats et fonctions à l'Assemblée nationale/) {
	mandat($p);
    }elsif (/Organismes extra-parlementaires/) {
	extra($p);	
    }elsif (/^Mandats (locaux|intercommunaux)/) {
	autre_mandat($p);	
    }elsif (/Place dans l'hémicycle/) {
	place($p);
    }
}

print "<Depute>\n";
foreach $k (keys %depute) {
    print "<$k>";
    if (ref($depute{$k}) eq 'ARRAY' ) {
	if (ref(${$depute{$k}}[0]) eq 'HASH') {
	    foreach $h (@{$depute{$k}}) {
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
    print "</$k>";
    print "\n";
}
print "</Depute>\n";
