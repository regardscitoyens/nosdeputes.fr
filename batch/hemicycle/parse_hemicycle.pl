#!/usr/bin/perl

$file = $url = shift;
#use HTML::TokeParser;
$url =~ s/^[^\/]+\///;
$url =~ s/_/\//g;
$source = $url;

if ($url =~ /\/(\d{4})\-(\d{4})[\/\-]/) {
    $session = $1.$2;
}

open(FILE, $file) ;
@string = <FILE>;
$string = "@string";
close FILE;

$string =~ s/<\/td><td>/: /g;
$string =~ s/<\/tr>/\n/g;
$string =~ s/ / /g;
$string =~ s/  +/ /g;
$string =~ s/\n/ /g;
$string =~ s/\\’85//g;
$string =~ s/<\/p>/<\/p>\n/g;
$string =~ s/(<\/h[1-9]>)/$1\n/g;
$string =~ s/(<h[0-9][^>]*>[^<]*)(<i>[^<]*<\/i>\s*)*/$1/gi;
$string =~ s/\s*(<i>\s*\([^\)]+\)\s*\.?\s*<\/i>)([,…–][^<\w]*)?\s*/$2<\/p>\n<p>$1<\/p>\n<p>/g;
$string =~ s/(<i>\s*\([^\)]+\s*<\/i>\s\)\s*\.?)/<\/p>\n<p>$1<\/p>\n<p>/g;
$string =~ s/<p><\/p>\n//g;
$string =~ s/(<br\s*\/>\s*)+/##BR##/g;

#Si italique dans gras, on vire (pb fonction)
while ($string =~ m/(M[me\.]+[ \&][^<]+<\/a>)\.[^<]*<\/b>[^<]*<i>\s*([^<]+)</g) {
    $fonc = $1;
    if ($fonc =~ /rapporteur|president/i) {
        $string =~ s/(M[me\.]+[ \&][^<]+<\/a>)\.[^<]*<\/b>[^<]*<i>\s*$fonc/$1,<\/b><i>/g;
}}
$string =~ s/([^\.])\s*<\/b>\s*<i>([^<]+)<\/i>/$1 $2<\/b>/g;
$string =~ s/<\/?font[^>]*>//ig;
$string =~ s/<\/?b>/|/g;
$string =~ s/\|\|//g;
$string =~ s/<\/?i>/\//g;
$string =~ s/\/\///g;
$string =~ s/\r//g;

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

if ($string =~ /ance est ouverte/i) {
    $heure = '09:30';
}
if ($string =~ /ouverte[^\.]+à ([^\.]+) heures?\s*([^\.\s]*)\s*\./) {
    $heure = $heure{$1}.':'.$heure{$2};
}

sub savepLoi() {
    $no =~ s/&nbsp;/ /g;
    $no =~ s/\s*et\s*/,/g;
    $no =~ s/[^\d,]//g;
    @no = split(/,/, $no);
    $no = '';
    foreach (@no) {
        s/(\d{4})(\d{4})/$1,$2/g;
        s/(\d{3})(\d{3})/$1,$2/g;
        s/^0+//;
        s/,0+//;
        $no .= $_.',';
    }
    chop $no;
    if ($no) {
        #print "TEST3 $titre -_- $no\n";
        $ploi{$titre} = $no;
    }
}

$string =~ s/&#8217;/'/g;
$string =~ s/<\/?sup>//g;
$string =~ s/<!--[^A-Z]+-->//g;
#Recherche des numéros de loi
while($string =~ /ordre du jour([^<]+\W(proposition|loi)\W[^<]+)\(n\D+(\d+[^\)]+)\)/ig) {
    if ($1 =~ /#item#/i) {
      next;
    }
    #print "TEST2 $1 -_- $2 -_- $3\n";
    $titre = lc $1;
    $no = $3;
    $titre =~ s/[^<]+ loi,? //;
    savepLoi();
}
while($string =~ /#item#\d+\.?\s*([^#]+)\(n\D+(\d+[^\)]+)\)\s*#\/item#/ig) {
    #print "TEST1 $1 -_- $2 \n";
    $titre = lc $1;
    $no = $2;
    savepLoi();
}

sub getProjetLoi {
    $titre_cleaned = $titre = lc shift;
    return unless ($titre);
    return $ploi{$titre} if (defined($ploi{$titre}));
    $intervention = lc shift;
    foreach $k (keys %ploi) {
        $k2 = $k;
        $k2 =~ s/[\(\)]/./g;
        if ($intervention =~ /$k2/i) {
            $ploi{$titre} = $ploi{$k};
            return $ploi{$k};
        }
    }
    return unless ($titre);
    return $ploi{$titre} if (defined($ploi{$titre}));
    $titre_cleaned =~ s/[^a-z]+/ /g;
    while ($titre_cleaned) {
        foreach $k (keys %ploi) {
            $_ = $k;
            s/[^a-z]+/ /g;
            if (/$titre_cleaned/) {
                $ploi{$titre} = $ploi{$k};
                return $ploi{$k};
            }
        }
        $titre_cleaned =~ s/^\s?\S+\s*//;
    }
    $ploi{$titre} = '';
    return ;
}


$cpt = 0;
sub checkout {
    $cpt+=10;
    $contexte = $titre1;
    if ($titre2) {
        $contexte .= ' > '.$titre2;
    }
    $contexte =~ s/"/\\"/g;
    $out =  '{"contexte": "'.$contexte.'", "date": "'.$date.'", "source": "'.$source.'", "heure":"'.$heure.'", "session": "'.$session.'", ';
    if (($ploi = getProjetLoi($titre1, $intervention)) && $contexte !~ /questions?\sau|ordre\sdu\sjour|bienvenue|(proclam|nomin)ation|suspension\sde\séance|rappels?\sau\srèglement/i) {
        $out .= "\"numeros_loi\": \"$ploi\", ";
    }
    if ($amendements) {
        $out .= '"amendements": "'.$amendements.'", ';
    }
    $out .= '"timestamp": "';
    if ($intervenant && $intervention) {
        $ts = $cpt;
        if ($intervention =~ s/^<p>(,| |et)+M[mes\.]*\s+(([A-Z]|é)[^\.]+)\.\s*/<p>/g) {
            $ts++;
            print $out.$ts.'", "intervention": "'.$intervention.'", "intervenant": "'.$2."\"}\n";
        }
        if ($intervenant =~ s/( et|, )(\s*M[mes\.]*|)\s*(([A-Z]|é).*)$//) {
            foreach $i (split(/ et |, /, $3)) {
                $ts++;
                print $out.$ts.'", "intervention": "'.$intervention.'", "intervenant": "'.$i."\"}\n";
            }
        }
        if ($inter2fonction{$intervenant} =~ s/( et|, )(\s*M[mes\.]*|)\s*(([A-Z]|é).*)//g) {
            $ts++;
            print $out.$ts.'", "intervention": "'.$intervention.'", "intervenant": "'.$3."\"}\n";
            $inter2fonction{$intervenant} = '';
        }
        print $out.$cpt.'", "intervention": "'.$intervention.'", "intervenant": "'.$intervenant.'", "fonction": "'.$inter2fonction{$intervenant}.'", "intervenant_url": "'.$intervenant_url."\"}\n";
    }elsif($intervention) {
        print $out.$cpt.'", "intervention": "'.$intervention.'", "intervenant":"'."\"}\n";
    }else {
        return ;
    }
    $commentaire = "";
    $intervenant = "";
    $intervenant_url = "";
    $intervention = "";
    $amendements = join ',', @pre_amendements;
}

sub setFonction {
    my $fonction = shift;
    $fonction =~ s/^\s*,\s*//;
    my $intervenant = shift;
    my $kfonction = lc($fonction);
    $kfonction =~ s/[^a-z]+/ /gi;
    $intervenant =~ s/\W+$//;
    $fonction2inter{$kfonction} = $intervenant;
    #print "$fonction ($kfonction)  => $intervenant \n";
    if (!$inter2fonction{$intervenant}) {
        $inter2fonction{$intervenant} = $fonction;
    }
}

sub setIntervenant {
    my $intervenant = shift;
    #print "$intervenant\n";
    $intervenant =~ s/^(M\.|Mme)([^  \s])/$1 $2/;
    $intervenant =~ s/[\|\/]//g;
    $intervenant =~ s/\s*\&\#8211\;\s*$//;
    $intervenant =~ s/\s*[\.\:]\s*$//;
    $intervenant =~ s/Madame/Mme/g;
    $intervenant =~ s/Monsieur/M./g;
    $intervenant =~ s/(\s+et|,)\s+M[\.lmes]+\s+/ et /g;
    $intervenant =~ s/^M[\.mes]*\s//i;
    $intervenant =~ s/([^M])\s*\..*$/\1/;
    $intervenant =~ s/L([ea])\s/l$1 /i;
    $intervenant =~ s/\s+\(/, /g;
    $intervenant =~ s/\)//g;
    $intervenant =~ s/[\.\,\s]+$//;
    $intervenant =~ s/^\s+//;
    $intervenant =~ s/É+/é/gi;
    $intervenant =~ s/\&\#8217\;/'/g;
    $intervenant =~ s/([^\s\,])\s+rapporteur/$1, rapporteur/i;
    $intervenant =~ s/M\. /M /;

    if ($intervenant =~ s/\,\s*(.*)//) {
        setFonction($1, $intervenant);
    }
    if ($intervenant =~ /^[a-z]/) {
        $intervenant =~ s/^l[ea]\s+//i;
        if ($intervenant =~ /([pP]résidente?|[rR]apporteur[a-zé\s]+)\s([A-Z].+)/) { #\s([A-Z].*)/i) {
            setFonction($1, $2);
            return $2;
        }
        $conv = $fonction2inter{$intervenant};
        if (!$conv) {
            $tmp = $intervenant;
            $tmp =~ s/[^a-z]+/ /gi;
            $conv = $fonction2inter{$tmp};
        }
    #    print "conv: '$conv' '$intervenant'\n";
        if ($conv) {
            $intervenant = $conv;
        }else {
            $test = lc($intervenant);
            $test =~ s/[^a-z]+/ /gi;
            foreach $fonction (keys %fonction2inter) {
                if ($fonction =~ /$test/) {
                    $inter = $fonction2inter{$fonction};
                    last;
                }
            }
            if (!$inter) {
                foreach $fonction (keys %fonction2inter) {
                    if ($test =~ /$fonction/) {
                        $inter = $fonction2inter{$fonction};
                        if ($test == $fonction."e") {
                            $inter2fonction{$inter} = lc($intervenant);
                        }
                        last;
                    }
                }
            }
            if ($inter) {
                $fonction2inter{lc($intervenant)} = $inter;
                $intervenant = $inter;
            }
        }
    }
    return $intervenant;
}

$string =~ s/\r//g;
$string =~ s/&nbsp;/ /g;
$string =~ s/&#339;|œ+/oe/g;
$string =~ s/&#8217;/'/g;
$string =~ s/\|(\W+)\|/$1/g;
$majIntervenant = 0;
$debut = 0;

$string =~ s/<br>\n*//gi;
$string =~ s/<\/?orateur>\n*//gi;
$string =~ s/\| et \|/ et /gi;

$string =~ s/##BR##([\|\/])/$1##BR##/g;
$string =~ s/##BR##(##BR##)+/##BR##/g;
$string =~ s/##BR##([\|\/])/$1##BR##/g;


#print "$string\n"; exit;

$donetitre1 = 0;
foreach $line (split /\n/, $string)
{
    if ($line =~ /DEBUT_SEANCE|séance est ouverte/) {
        $debut = 1;
    }
    #recherche amendements
    if ($line =~ /\<\!\-\- AMEND_/) {
        @pre_amendements = ();
        while ($line =~ /\<\!\-\- AMEND_(\d+)\D/g) {
            push @pre_amendements, $1;
        }
    }

    #suppression des commentaires
    $line =~ s/\<\!\-\-[^\>]+\>//g;
    #si deux intervenant en même temps
    $line =~ s/\|\s*et\s*\|/ et /gi;
    #si italique ou gras sans raison on supprime
    $line =~ s/([^p][^:])\/\s*\//$1 /g; #évite de le faire pour http://
    $line =~ s/\|\s*\|/ /g;
    $line =~ s/ de loi de loi / de loi /g;


    #récupère les ancres pour de meilleurs liens sources
    if ($line =~ /\<[a]/i) {
        if ($line =~ s/<a name=["']([^"']+)["'][^<]+<[^>]+>/<</g) {
            $nextsource = $url."#$1";
        }
    }

    if ($line =~ /<h[1-9]+/i || $line =~ /"(sompresidence|sstitreinfo)"/) {
        $line =~ s/##BR##/ /g;
        if ($line =~ /pr..?sidence de ([^<\,]+)[<,]/i && $line !~ /sarkozy/i) {
            $prez = $1;
            $prez =~ s/\s+vice-pr.*$//;
    #        print "Présidence de $prez\n";
            if ($prez =~ /^Mm/) {
                setFonction('présidente', $prez);
            }else {
                setFonction('président', $prez);
            }
        }elsif($line =~ /h2 class="titre[23]"><*([^<\(]+)\s*/ || $line =~ /class="sstitreinfo">\/([^\/]+)\//) {
            checkout();
            if (($1 !~ /suspension/ && $1 !~ /séance/) || $1 =~ /demande/i){
                $titre2 = $1;
            }
            $titre2 =~ s/a href[^>]+>//g;
            $titre2 =~ s/\///g;
            $titre2 =~ s/\s+$//;
            $titre2 =~ s/h2>//gi;
            $amendements = @pre_amendements = ();
            $line = "<p>|$titre2|</p>";
            $donetitre1 = 0;
        }elsif(!$donetitre1 && $line =~ /h2 class="titre1">(.+)<\/h2/i) {
            checkout();
            $titre = $1;
            $titre =~ s/<\/?[^>]+>//g;
            $titre =~ s/<//g;
            $titre =~ s/[\(\/][^\)\/]+[\)\/]//;
            $titre =~ s/\///g;
            $titre =~ s/\s+$//;
            unless ($titre) {
                next;
            }
            if ($titre =~ /^[\/\s]*[\wéè]+ \s*partie[\/\s]*(suite[\/\s]*|)$/i || $titre =~ /^\s*[\(\/]+.*[\/\)]+\s*$/) {
                next
            }
            if ($titre !~ /rappels? au règlement/i) {
                $donetitre1 = 1;
            }
            $titre1 = $titre;
            $titre2 = '';
            $amendements = @pre_amendements = ();
            $line = "<p>|$titre1|</p>";
        }elsif($line =~ /h1 class="seance"/) {
            if ($line =~ /(\d{1,2})[ermd]*\s+([a-zéùû]+)\s+(\d{4})/) {
                $date = $3.'-'.$mois{$2}.'-'.sprintf('%02d', $1);
            }
        }elsif($line =~ /h5 class="numencad"/) {
            $donetitre1 = 0;
        }
    }

    next unless ($debut);

    $line =~ s/<<//g;
#    $line =~ s/<\/?p>//g;

#    print STDERR "$titre1 > $titre2 : $line\n" ; next;
    $line =~ s/\|\///;
    if ($line =~ /\<[p]/i) {
        $last_href = '';
        if ($line =~ /href=["']([^"']+)["']/) {
            $last_href = $1;
        }
        $line =~ s/\<\/?[^\>]+\>//g;
        last if ($line =~ /^\|annexe/i);
        next if ($line !~ /\w/);
        #cas des intervenants en gras suivi immédiatement de la fonction en italique
        $line =~ s/^(\s*\|\s*M[^\|]+)(\s*,\s*\|\s*|\s*\|\s*,\s*)\/([^\/]+)\//$1, $3|/;
        #si italique ou tout gras => commentaire
        $line =~ s/##BR##$//;
        foreach $line (split /##BR##/, $line) {
            if ($line =~ /^\s*\|.*\|\s*$/ || $line =~ /^\s*\/[^\/]*[\/\)\.\s]*$/) {
                $oldintervenant = $intervenant;
                $oldintervenant_url = $intervenant_url;
                checkout() if ($intervenant);
                $intervenant = ''; $intervenant_url = '';
                if ($line =~ /^[\s\/\.]*\([^\)]+\)[\s\/\.]*$/) {
                    checkout() if ($intervention);
                    clean_line();
                    $intervention = "<p>$line</p>";
                    $intervention =~ s/[\(\)]//g;
                    checkout();
                    $intervenant = $oldintervenant;
                    $intervenant_url = $oldintervenant_url;
                    next;
                }
            }elsif ($line =~ s/^\s*\|\s*(M[^\|\/\:]+)[\|\/\:]// || $line =~ s/^\s*(M[\.Mmle]+(\s+([dl][eaus'\s]+)*[^\.:\s]{2,}){1,4})[\.\:]//) {
                checkout();
                $majIntervenant = 1;
                $intervenant = setIntervenant($1);
                $intervenant_url = $last_href;
                $intervenant_url = "http://www.assemblee-nationale.fr".$last_href if ($intervenant_url =~ /^\//);
                $found = 1;
            }elsif ($line =~ s/^\|([^\|]+)\|\s*//) {
                checkout();
                $intervenant = setIntervenant($1);
            }elsif ($line =~ /^\s*\|/) {
                checkout() if ($intervenant);
            }
            clean_line();
            if ($line =~ /^\s*$/) {
                next;
            }
            $intervention .= "<p>$line</p>";
            $source = $nextsource;
        }
    }
}
if ($intervention =~ "^<p>Le Directeur du service du compte rendu de la séance") {
    $intervenant = "";
}
checkout();

sub clean_line{
    $line =~ s/[\|\/]//g;
    $line =~ s/^\s*[\.\:]\s*//;
    $line =~ s/"/\\"/g;
    $line =~ s/\s+/ /g;
    $line =~ s/^\s//;
    $line =~ s/\s$//;
}
