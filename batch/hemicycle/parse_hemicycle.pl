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

$string =~ s/ / /g;
$string =~ s/(&#8211;)/–/g;
$string =~ s/(’|&#8217;)/'/g;
$string =~ s/(\.|,|:|;|…|–)(,|:|;|…|–)/\1 \2/g;
$string =~ s/(!|\?|\.|:|;|…|–)([^\s<!?\.:;…–])/\1 \2/g;
$string =~ s/  +/ /g;
$string =~ s/\n/ /g;
$string =~ s/\\’85//g;
$string =~ s/\s*<\/sup><i>\s*/ <i>/ig;
$string =~ s/<\/?sup>//g;
$string =~ s/<\/p>/<\/p>\n/g;
$string =~ s/(<\/h[1-9]>)/$1\n/g;
$string =~ s/(<h[0-9][^>]*>.*?)<i>\(suite[^)]*?\)<\/i>/\1/gi;
$string =~ s/<\/i>(\W+)<i>/\1/ig;
$string =~ s/no<\/i>s <i>/nos /ig;
$string =~ s/(<i><\/i>|<\/i><i>)//ig;
$string =~ s/(<h[0-9][^>]*>.*?)\s*-\s*Suite(\)<\/i><\/h2>)/\1\2>/gi;
$string =~ s/(<h[0-9][^>]*>[^<]*)[\s(]*<i>[\s(]*Suite.*?<\/i>[\s)]*/\1/gi;
$string =~ s/(<h[0-9][^>]*>.*?)<i>(.*?)<\/i>(.*?<\/h2>)/\1\2\3/gi;
$string =~ s/(<(h[0-9])[^>]*>)\s*(<i>[^<]*<\/i>)\s*<\/\2>/\1<\/\2>\n<p>\3<\/p>/gi;
$string =~ s/\s*…\s*(<\/i>)?\s*(<br\s*\/?>\s*)+…\s*/\1 /gi;
$string =~ s/\((\s*)<i>/\1<i>(/ig;
$string =~ s/<\/i>(\s*)\)/)<\/i>\1/ig;
$string =~ s/(\(([^)]*?amendement[^)]*?(adopt|rejet)|Nouveaux|Applaudissement|Exclamation|Vif|Vive|Quelque|M..?mes? mouve|(Sou)?Rires|[^)]*?bancs d[esu]+ groupe)[^)]*\))/<i>\1<\/i>/ig;
$string =~ s/(<\/?i>)([–,\.\s]*)\1/\1\2/ig;
$string =~ s/<\/i>\s*<i>\s*\(\s*/<\/i> <i>(/ig;
$string =~ s/\)((,|\.|…|!|\?)\s*)<\/i>/)<\/i>\1/ig;
$string =~ s/\s*(<i>\s*\([^\)]+\)[\s\.]*<\/i>|\(<i>[^\)]+?<\/i>\))((,|\s*[…–:;!?])+\s*[^<\wàâéèêëïîôöùûü«]*)?\s*/$2<\/p>\n<p>$1<\/p>\n<p>/ig;
$string =~ s/(<i>\s*\([^\)]+\s*<\/i>\s\)\s*\.?|\(<i>[^\)]+?<\/i>\))/<\/p>\n<p>$1<\/p>\n<p>/g;
$string =~ s/<p>\s*<\/i>\s*/<p>/ig;
$string =~ s/\s*<i>\s*<\/p>/<\/p>/ig;
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

$string =~ s/<t([rdh])[^>]*( (row|col)span=["\d]+)+[^>]*>/<t\1\2>/gi;
$string =~ s/<t([rdh])( (row|col)span=["\d]+)*[^>]*>/<t\1\2>/gi;
$string =~ s/\n+\s*(<\/?t(able|[rdh]))/\1/gi;
$string =~ s/(<\/table>)\s*(<table|<p)/\1\n\2/gi;
$string =~ s/(<\/p>)\s*(<table)/\1\n\2/gi;
$string =~ s/(<img[^>]*)[\n\r]+([^>]*>)/\1 \2/gi;

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

$heures{'zéro'} = '00';
$heures{'une'} = '01';
$heures{'deux'} = '02';
$heures{'trois'} = '03';
$heures{'quatre'} = '04';
$heures{'cinq'} = '05';
$heures{'six'} = '06';
$heures{'sept'} = '07';
$heures{'huit'} = '08';
$heures{'neuf'} = '09';
$heures{'dix'} = '10';
$heures{'onze'} = '11';
$heures{'douze'} = '12';
$heures{'treize'} = '13';
$heures{'quatorze'} = '14';
$heures{'quinze'} = '15';
$heures{'seize'} = '16';
$heures{'dix-sept'} = '17';
$heures{'dix-huit'} = '18';
$heures{'dix-neuf'} = '19';
$heures{'vingt'} = '20';
$heures{'vingt et une'} = '21';
$heures{'vingt-et-une'} = '21';
$heures{'vingt-deux'} = '22';
$heures{'vingt-trois'} = '23';
$heures{'vingt-cinq'} = '25';
$heures{'trente'} = '30';
$heures{'trente-cinq'} = '35';
$heures{'quarante'} = '40';
$heures{'quarante-cinq'} = '45';
$heures{'cinquante'} = '50';
$heures{'cinquante-cinq'} = '55';
$heures{''} = '00';

if ($string =~ /ouverte[^\.]+à\s+([^\.]+?)\s*heures?\s*([^\.\s]*)\s*\./) {
    $heure = $heures{$1}.':'.$heures{$2};
} else {
    print STDERR "ERROR: cannot find start hour in séance $url\n";
    exit(1);
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
    $intervention =~ s/\\\\/\//g;
    $intervention =~ s/\s*(<\/?t(able|[rdh])[^>]*>)\s*/\1/gi;
    $out =  '{"contexte": "'.$contexte.'", "date": "'.$date.'", "source": "'.$source.'", "heure": "'.$heure.'", "session": "'.$session.'", ';
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
            $i = $2;
            if (!$inter2fonction{$i} && $i =~ s/, (.*)$//) {
                setFonction($1, $i);
            }
            print $out.$ts.'", "intervention": "'.$intervention.'", "intervenant": "'.$i.'", "fonction": "'.$inter2fonction{$i}."\"}\n";
        }
        if ($intervenant !~ /^plusieurs /i && $intervenant =~ s/( et|, )(\s*M[mes\.]*|)\s*(([A-Z]|é).*)$//) {
            foreach $i (split(/(?:et\s*M[mes\.]*| et |, M[mes\.]*)\s*/, $3)) {
                $ts++;
                if (!$inter2fonction{$i} && $i =~ s/, (.*)$//) {
                    setFonction($1, $i);
                }
                print $out.$ts.'", "intervention": "'.$intervention.'", "intervenant": "'.$i.'", "fonction": "'.$inter2fonction{$i}."\"}\n";
            }
        }
        if ($inter2fonction{$intervenant} =~ s/( et|, )(\s*M[mes\.]*|)\s*(([A-Z]|é).*)//g) {
            $ts++;
            print $out.$ts.'", "intervention": "'.$intervention.'", "intervenant": "'.$3."\"}\n";
            $inter2fonction{$intervenant} = '';
        }
        $extrafct = "";
        if ($intervenant =~ s/\s*(Doyenn?e? d'âge)$//i) {
            $extrafct = ", ".lc($1);
        }
        print $out.$cpt.'", "intervention": "'.$intervention.'", "intervenant": "'.$intervenant.'", "fonction": "'.$inter2fonction{$intervenant}.$extrafct.'", "intervenant_url": "'.$intervenant_url."\"}\n";
    }elsif($intervention) {
        print $out.$cpt.'", "intervention": "'.$intervention.'", "intervenant": "'."\"}\n";
    }else {
        $cpt-=10;
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
    $intervenant =~ s/(\s+et|,)+\s+M[\.lmes]+\s+/ et /g;
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
    if ($line =~ /<!-- AMEND_/) {
        @pre_amendements = ();
        while ($line =~ /<!-- AMEND_(\d+)\D/g) {
            push @pre_amendements, $1;
        }
    }

    #suppression des commentaires
    $line =~ s/<!--[^\>]*>//g;
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
        if ($line =~ /pr..?sidence de ([^<\,]+)[<,]/i && $line !~ /sarkozy|télévision/i) {
            $prez = $1;
            $prez =~ s/\s+vice-pr.*$//;
    #        print "Présidence de $prez\n";
            if ($prez =~ /^Mm/) {
                setFonction('présidente', $prez);
            }else {
                setFonction('président', $prez);
            }
        }elsif($line =~ /h2 class="titre[2-9]+">(.*?)\s*<\/h2>/i || $line =~ /class="sstitreinfo">\/([^\/]+)\//) {
            checkout();
            $tmpline = $1;
            $tmpline =~ s/<\/?[a-z][^>]*>//g;
            next unless ($tmpline);
            if (lc($tmpline) ne lc($titre1) && ($tmpline !~ /rappels? au règlement|suspension|reprise/i || $tmpline =~ /demande/i)) {
                $titre2 = $tmpline;
                $donetitre1 = 0;
                $amendements = @pre_amendements = ();
            }
            $line = "<p>|$tmpline|</p>";
        }elsif(!$donetitre1 && $line =~ /h2 class="titre1">(.+)<\/h2/i) {
            checkout();
            $titre = $1;
            $titre =~ s/<\/?[^>]+>//g;
            $titre =~ s/<//g;
            $titre =~ s/[\(\/][^\)\/]+[\)\/]//;
            $titre =~ s/\///g;
            $titre =~ s/\s+$//;
            next unless ($titre);
            if ($titre !~ /rappels? au règlement|suspension|reprise/i) {
                $titre1 = $titre;
                $titre2 = '';
                $donetitre1 = 1;
                $amendements = @pre_amendements = ();
            }
            $line = "<p>|$titre|</p>";
        }elsif($line =~ /h1 class="seance"/) {
            if ($line =~ /(\d{1,2})[ermd]*\s+([a-zéùû]+)\s+(\d{4})/) {
                $date = $3.'-'.$mois{$2}.'-'.sprintf('%02d', $1);
            }
        }elsif($line =~ /h5 class="numencad"/) {
            $donetitre1 = 0;
        }
    }

    next unless ($debut);

    # Rewrite img urls
    while ($line =~ /^(.*)<(img.*? src=.)(.*?)(['"][^\>]+)>(.*)$/i) {
      $img0 = $1;
      $img1 = $2;
      $img2 = $4;
      $img3 = $5;
      $imgurl = $3;
      if ($imgurl =~ /^\//) {
        $imgurl = $rooturl.$imgurl
      } elsif ($imgurl !~ /^http/i) {
        $imgurl = $baseurl.$imgurl;
      }
      $imgurl =~ s/[\/]/\\\\/g;
      $img2 =~ s/[\\]/\\\\/g;
      $line = $img0."##".$img1.$imgurl.$img2."##".$img3;
    }

    if ($prez && $line =~ /<\/?t(able|d|h|r)/) {
        $line =~ s/([^<])[\/\|]/\1/g;
        $line =~ s/<[^t\/][^>]*>//g;
        $line =~ s/<\/[^t][^>]*>//g;
        $line =~ s/"/\\"/g;
        checkout() if ($intervenant || ($line =~ /<table/ && length($intervention) + length($line) gt 2000));
        $intervention .= "$line";
        next;
    }

    $line =~ s/<<//g;
#    $line =~ s/<\/?p>//g;

    #print STDERR "$titre1 > $titre2 : $line\n" ; next;
    $line =~ s/\|\///;
    if ($line =~ /\<[p]/i) {
        $last_href = '';
        if ($line =~ /href=["']([^"']+)["']/) {
            $last_href = $1;
        }
        $line =~ s/\<\/?[^\>]+\>//g;
        $line =~ s/##(img[^\>#]+?)##/<\1 \\\\>/ig;
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
                if ($line) {
                    checkout();
                    $majIntervenant = 1;
                    $intervenant = setIntervenant($1);
                    $intervenant_url = $last_href;
                    $intervenant_url = "http://www.assemblee-nationale.fr".$last_href if ($intervenant_url =~ /^\//);
                } else {
                    $line .= $1.".";
                }
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
            $line =~ s/\s*«\s*$//;
            $intervention .= "<p>$line</p>";
            $source = $nextsource;
        }
    }
}
if ($intervention =~ /^<p>L[ea] Direct(eur|rice) du service du compte rendu de la séance/) {
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
