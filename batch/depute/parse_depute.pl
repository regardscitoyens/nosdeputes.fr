#!/usr/bin/perl

use HTML::TokeParser;
use HTML::Entities;
use Encode;
require "./finmandats.pm";
require "../common/common.pm";

$file = shift;
$yml = shift || 0;
$display_text = shift;

open(FILE, $file);
@string = <FILE>;
$string = "@string";
close FILE;
$string =~ s/\r//g;
$string =~ s/\&nbsp;?/ /ig;
$string =~ s/Univerist/Universit/g;
$string =~ s/aglommération/agglomération/g;
$string =~ s/[\n\s]+/ /g;
$string =~ s/^.*(<h1 class="deputy-headline-title)/\1/i;
$string =~ s/<div id="actualite".*<\/div>(<div id="fonctions")/\1/i;
$string =~ s/<div id="travaux".*$//i;
while ($string =~ s/(<li class="contact-adresse">([^<]*)?)(<\/?p>)+(.*<\/li>(<li class="contact-adresse">|<\/ul>))/\1 \4/gi) {}
$string =~ s/(<(div|p|ul|\/li|abbr|img|dt|dd|h\d)[ >])/\n\1/ig;
$string =~ s/<\/?sup>//ig;
$string =~ s/<svg[^>]*>.*?<\/svg>//ig;
$string =~ s/\s*'\s*/'/g;

if ($display_text) {
  print $string;
  exit;
}

my %depute;
my %groupes;
my %orgas;
my %mission;

sub clean_vars {
  $encours = $lieu = $organisme = $fonction = "";
  $mission = 0;
  $missioninfo = 0;
}

my %premiers_mandats;
sub add_mandat {
  $start = shift;
  $end = shift;
  $cause = shift;
  if ($cause =~ /(remplacement.*)\s*:\s*(.*)\s*$/i && $cause !~ /lection/i && !$depute{'suppleant'}) {
    $depute{'suppleant_de'} = $2;
    $cause =~ s/\s*:\s*(.*)\s*$/ \(\1\)/;
  }
  $cause =~ s/^É/é/;
  $cause =~ s/(du gouvernement) :.*$/\1/i;
  $premiers_mandats{"$start / $end / ".lc($cause)} = 1;
  $depute{'debut_mandat'} = max_date($start,$depute{'debut_mandat'});
  $depute{'fin_mandat'} = max_date($end,$depute{'fin_mandat'}) if ($end !~ /^$/ && ($start == $end || max_date($end,$depute{'debut_mandat'}) != $depute{'debut_mandat'} ));
}

if ($file =~ /(\d+)/) {
  $depute{'id_institution'} = $1;
  $depute{'url_institution'} = "http://www2.assemblee-nationale.fr/deputes/fiche/OMC_PA$1";
  $depute{'old_url_institution'} = "http://www.assemblee-nationale.fr/$legislature/tribun/fiches_id/$1.asp";
  $depute{'photo'} = "http://www2.assemblee-nationale.fr/static/tribun/$legislature/photos/$1.jpg";
  $depute{'old_photo'} = "http://www.assemblee-nationale.fr/$legislature/tribun/photos/$1.jpg";
}

$read = "";
$parti = "";
$address = "";
$done = 0;
foreach $line (split /\n/, $string) {
  #print STDERR "$line\n";
  $line =~ s/<\/?sup>//g;
  if ($line =~ /<h1>(.+)<\/h1>/i) {
    $depute{'nom'} = $1;
    $depute{'nom'} =~ s/,.*$//;
    $depute{'nom'} =~ s/[\- ]*Président.*$//;
    $depute{'nom'} =~ s/^(M[.mle]+) //;
    if ($1 =~ /e/) {
      $depute{'sexe'} = "F";
    } else {
      $depute{'sexe'} = "H";
    }
  } elsif (!$depute{'circonscription'} && $line =~ /(<ul> <li>|"deputy-head?line-sub-title">)([^<]*) \((\d+[èrme]+) circonscription/i) {
    $depute{'circonscription'} = "$2 ($3)";
  } elsif ($line =~ /Née? le ([0-9]+e?r? \S+ [0-9]+)( [àaux]+ (.*))?/i) {
    $depute{'date_naissance'} = join '/', reverse datize($1);
    $lieu = $3;
    $lieu =~ s/\s*\(\)\s*//g;
    $lieu = trim($lieu);
    $depute{'lieu_naissance'} = $lieu if ($lieu !~ /^$/);
    $read = "profession";
  } elsif ($line =~ /<dt>Suppléant<\/dt>/i) {
    $read = "suppleant";
  } elsif ($line =~ /<dt>Rattachement au titre du financement/i) {
    $read = "parti";
  } elsif ($line =~ /<dl class="adr">/i) {
    $read = "adresse";
    $address = "";
  } elsif ($line =~ /<dd class="tel">.*<span class="value">([^<]*)</i) {
    delete $depute{'adresses'}{$address};
    $line =~ s/<[^>]+>//g;
    $address .= " ".trim($line);
    $depute{'adresses'}{$address} = 1;
  } elsif ($read !~ /^$/) {
    if ($read =~ /adresse/) {
      if ($line =~ /<dd/i) {
        $address .= $line;
        $address =~ s/<[^>]+>//g;
        if ($line =~ /<\/dl>/i) {
          $address = trim($address);
          $depute{'adresses'}{$address} = 1;
          $read = "";
        }
      }
    } elsif ($line =~ /<dt>/i) {
      $read = "";
    } else {
      $line =~ s/<[^>]+>//g;
      $line = trim($line);
      $depute{"$read"} = $line if ($line !~ /^$/);
      if ($read =~ /suppleant/) {
        $depute{"$read"} =~ s/[(,\s]+décédé.*$//i;
        $depute{"$read"} =~ s/Mlle /Mme /;
        $depute{"$read"} =~ s/([A-ZÀÉÈÊËÎÏÔÙÛÜÇ])(\w+ ?)/\1\L\2/g;
      }
      $read = "" if ($line !~ /^$/);
    }
  } elsif ($line =~ /composition du groupe"[^>]*>([^<]+)</i) {
    $groupe = lc($1);
    $groupe =~ s/É/é/g;
    $groupe =~ s/^union pour la démocratie française$/union des démocrates et indépendants/;
    $groupe =~ s/^rassemblement pour la république$/union pour un mouvement populaire/;
    $groupe =~ s/^socialiste$/socialiste, républicain et citoyen/;
    $groupe =~ s/^non inscrit$/Députés non inscrits/;
    if ($line =~ /(apparentée?|présidente?)( du groupe)? /i) {
      $gpe = $groupe." / ".(lc $1);
    } else {
      $gpe = $groupe." / membre";
    }
    $gpe .= "e" if ($depute{'sexe'} eq "F" && $gpe =~ /(président|apparenté)$/);
    $depute{'groupe'}{$gpe} = 1;
  } elsif ($line =~ /mailto:([^'"]+@[^'"]+)['"]/i) {
    $depute{'mails'}{$1} = 1;
  } elsif ($line =~ /<a [^>]*class="(url|facebook|twitter topmargin)" *href=['"]([^"']+)['"]/i) {
    $site = $2;
    if ($1 =~ /twitter/) {
      $site =~ s/\/$//;
    }
#    $site =~ s#^(http://| )*#http://#i; #Bug plus d'actualité ?
    if ($site =~ s/(https?:\/\/)?([^\/]+@[^\/]+)$/\2/) { #Les url twitter sont indiquées avec un @
      $depute{'mails'}{$site} = 1;
    } else {
      if ($site !~ /www.facebook\.com.sharer\.php/) { #Evite de prendre les boutons de partage de l'AN
        $depute{'sites_web'}{$site} = 1;
      }
    }
  } elsif ($line =~ /id="hemicycle-container" data-place="(\d+)">/i) {
    $depute{'place_hemicycle'} = $1;
  } elsif ($line =~ /\(Date de début de mandat[\s:]+([\d\/]+)( \((.*)\)\))?/i) {
    add_mandat($1,"",$3);
  } elsif ($line =~ /Mandat du ([\d\/]+)([ <!\-]+\(.*\))?[ >!\-]+au ([\d\/]+)( \((.*)\))?/i) {
    add_mandat($1,$3,$5);
#  } elsif ($line =~ /(Reprise de l'exercice.*député.*) le[ :]+([\d\/]+)/) {
#    add_mandat($2, "", $1);
  } elsif ($line =~ /Anciens mandats et fonctions à l'Assemblée nationale/) {
     $done = 1;
     $encours = "";
  } elsif ($line =~ /<!--fin.*tab.*-->/) {
     $encours = "";
  } elsif ($line =~ /^<h4 class/ && !$done) {
    clean_vars();
    $line =~ s/\s*<[^>]+>\s*/ /g;
    $line =~ s/[  \s]+/ /g;
    $line = trim($line);
    if ($line =~ /(Bureau|Commissions?|Missions? (temporaire|d'information)s?|Délégations? et Offices?)/) {
      $encours = "fonctions";
      if ($line =~ /Missions? temporaires?/) {
        $mission = 1;
      } elsif ($line =~ /information/) {
        $missioninfo = 1;
      }
    } elsif ($line =~ /(Organismes? extra-parlementaires?|Fonctions? dans les instances internationales ou judiciaires)/) {
      $encours = "extras";
    } elsif ($line =~ /Mandats? (loca[lux]+ en cours|intercommuna)/i) {
      $encours = "autresmandats";
    } elsif ($line =~ /Groupes? d'(études?|amitié)/i) {
      $encours = "groupes";
      $type_groupe = $line;
    }
  } elsif ($encours !~ /^$/ && $line !~ /^<h[23]/i) {
    #print STDERR "TEST $encours: $line\n";
    $oline = $line;
    $line =~ s/\s*<[^>]+>\s*/ /g;
    $line =~ s/([^à]+)[  \s]+/\1 /g;
    $line = trim($line);
    next if ($line =~ /^$/);
    if ($oline =~ /<span class="dt">/i) {
      $line =~ s/^\(((Président|Rapporteur)(e)?( (général|spécial))?).*\)$/\1\3/;
      $line =~ s/Rapporteur(e)? sur .*$/rapporteur\1 thématique/i;
      $line =~ s/\([^)]*\)//i;
      $line =~ s/délégue/délégué/i;
      $line =~ s/ par le Président de l'Assemblée nationale\s*//i;
      $fonction = lc $line;
      next;
    } elsif ($encours =~ /anciensmandats/) {
      if ($line =~ /du (\d+\/\d+\/\d+) au (\d+\/\d+\/\d+) \((.*)\)/i) {
        $dates = "$1 / $2";
        $fonction = $3;
        $tmporga = lc($organisme);
        $tmporga =~ s/\W/./g;
        $fonction =~ s/\s*(d[elau'\s]+)?$tmporga\s*//i;
        if (!$orgas{trim($lieu)." / ".trim($organisme)}) {
          $depute{$encours}{trim($lieu)." / ".trim($organisme)." / ".trim($fonction)." / $dates"} = 1;
          $orgas{trim($lieu)." / ".trim($organisme)} = 1;
        }
      } elsif ($line =~ /^\s*(.[^A-Z\(]+) d(e la |[ue]s? |'|e l')([A-ZÀÉÈÊËÎÏÔÙÛÇ].*)$/) {
#      } elsif ($line =~ /^\s*(.[^(A-ZÀÉÈÊËÎÏÔÙÛÇ]*) d([ue](s| la)? |'|e l')(\U.*)$/) {
        $organisme = $1;
        $lieu = $3;
        $organisme = "Conseil de Paris" if ($lieu =~ s/ \(Département de Paris\)/ (Département)/);
      } else {
        $line =~ s/Communauté Agglomération/Communauté d'agglomération/i;
        $lieu = $line;
        if ($line =~ /^\s*c(ommunauté d?[elau'\s]*\S+) (d[elasu'\s]+)?(\U.*)$/i) {
          $organisme = "C$1";
          $lieu = $3;
        } else {
          $organisme = "Communauté d'agglomération";
        }
      }
    } elsif ($encours =~ /autresmandats/) {
      if ($line =~ /^\s*(.*?) (de la )?c(ommunauté (urbaine|d[elaus'\s]+\S+)) (d[elsau\s]*?['\s])?(\U.*)$/i) {
        $fonction = lc $1;
        $organisme = "C".(lc $3);
        $lieu = $6;
        $organisme =~ s/[cC](ommunauté d)(e (communes? de )?l)?'[aA](gglomération)s?/C\1'a\4/;
        $organisme =~ s/(Communauté de commune)$/\1s/;
      } else {
        $lieu = "";
        $line =~ s/(Con(seil|grès)|Gouvernement)/\L\1/;
        if ($line =~ s/^([^(]*?) d([ue](s| la)? |'|e l')([A-ZÀÉÈÊËÎÏÔÙÛÇ].*)$/\1/) {
          $lieu = $4;
        } elsif ($line =~ s/^(.*)\(([A-ZÀÉÈÊËÎÏÔÙÛÇ].*)\)$/\1/) {
          $lieu = $2;
        }
        $lieu =~ s/(Paris|Lyon|Marseille) \(?(\d+[erèm]+ (Arrondissement|secteur))\)?.*$/\1 \2/i;
        $line =~ s/\s+$//;
        $organisme = ucfirst($4) if ($line =~ s/^(.*) d((u|e la) |e l')(.*)$/\1/);
        $fonction = lc $line;
        $fonction =~ s/ du$//;
        if ($fonction =~ /maire/i || $fonction =~ s/^(conseillere? )municipal (déléguée?)/\1\2/) {
          $organisme = "Conseil municipal";
        }
      }
      $lieu =~ s/, (.*)$/ (\1)/;
      $hashstr = trim(ucfirst($lieu))." / ".trim($organisme);
      if (!$orgas{$hashstr}) {
        $depute{$encours}{$hashstr." / ".trim($fonction)} = 1;
        $orgas{$hashstr} = 1;
      }
    } elsif ($encours =~ /groupes/) {
      $line =~ s/Groupe d'études //;
      $type = "Groupe d'amitié ";
      $type = "Groupe d'études " if ($type_groupe =~ /étude/i);
      $line =~ s/\(République du\)/(République démocratique du)/i;
      if (!$groupes{$line}) {
        $groupes{$line} = 1;
        if (!$orgas{$type.trim($line)}) {
          $depute{$encours}{$type.trim($line)." / ".lc(trim($fonction))} = 1;
          $orgas{$type.trim($line)} = 1;
        }
      }
    } else {
      if ($mission && $line =~ /^(.*?)\(?((Premier ministre|Ministère|Secrétariat).*)\)?\s*$/) {
        $organisme = trim($1);
        $minist = trim($2);
        $minist =~ s/ - (Premier min|Minist|Secr).*$//;
        $minist =~ s/[^a-zàéèêëîïôù]+$//;
        $minist = "Mission temporaire pour le $minist";
        if ($organisme !~ /^$/) {
          $organisme =~ s/^La proposition /Proposition /;
          $organisme = "$minist : $organisme";
        } else {
          $organisme = $minist;
        }
        $fonction = "chargé".($depute{'sexe'} eq "F" ? "e" : "")." de mission";
      } elsif ($line =~ s/ de l'Assemblée nationale depuis le : \d.*$//) {
        $organisme = "Bureau de l'Assemblée nationale";
        $fonction = lc $line;
      } else {
        $organisme = ucfirst($line);
        $organisme =~ s/("|\(\s*|\s*\))//g;
        if ($missioninfo && $organisme !~ /^Mission/) {
          $organisme = "Mission d'information $organisme";
        }
        $organisme =~ s/( et de contrôle)\s*$/\1 de la commission des finances/;
      }
      if (!$orgas{trim($organisme)}) {
        $depute{$encours}{trim($organisme)." / ".trim($fonction)} = 1;
        $orgas{trim($organisme)} = 1;
      }
    }
  }
}

#On récupère le nom de famille à partir des emails
$nomdep = $depute{'nom'};
@noms = split / /, $nomdep;
if ((join " ", keys %{$depute{'mails'}}) =~ /(\S+)\@assemblee/) {
  $login = $1;
  while ($login = substr($login, 1)) {
    $clogin = $login;
    $clogin =~ s/[ce]/./ig;
    $clogin =~ s/\.+/.+/g;
    for($i = 0 ; $i <= $#noms ; $i++) {
      next if ($noms[$i] =~ /^[ld]e/i);
      $tmpnom = lc($noms[$i]);
      $tmpnom =~ s/[àÀéÉèÈêÊëËîÎïÏôÔùÙûÛçÇ]/./ig;
      $tmpnom =~ s/\.+/.+/g;
      if ($login =~ /$tmpnom/i) {
        if ($nomdep =~ /.\s([l][ea]s?\s)?(\S*?$tmpnom.*$)/i) {
          $depute{'nom_de_famille'} = $1.$2;
          last;
        }
      }
    }
    if ($depute{'nom_de_famille'}) {
      last;
    }
  }
}
#Si pas de nom de famille, on le récupère par le nom
if (!$depute{'nom_de_famille'}) {
  if ($depute{'nom'} =~ /\S (des? )?(.*)$/i) {
    $depute{'nom_de_famille'} = $2;
  }
}
$depute{'nom_de_famille'} = trim($depute{'nom_de_famille'});

#clean doublons mandats
my %tmp_mandats;
foreach $m (keys %premiers_mandats) {
  $date1 = $m;
  $date1 =~ s/ \/ .*$//;
  if (!$tmp_mandats{$date1} || $tmp_mandats{$date1} =~ / \/ +\/ /) {
    $tmp_mandats{$date1} = $m;
  }
}
foreach $m (values %tmp_mandats) {
  $depute{'premiers_mandats'}{$m} = 1;
}
if ($depute{"parti"}) {
  if ($depute{"parti"} =~ s/Non rattaché\(s\)/Non rattaché/i && $depute{"sexe"} eq "F") {
    $depute{"parti"} .= "e";
  };
  $depute{"parti"} =~ s/ \(Debout la République\)//i;
}

if ($yml) {
  print "  depute_".$depute{'id_an'}.":\n";
  foreach $k (keys %depute) {
    if (ref($depute{$k}) =~ /HASH/) {
      print "    ".lc($k).":\n";
      foreach $i (keys %{$depute{$k}}) {
        print "      - $i\n";
      }
    } else {
      print "    ".lc($k).": ".$depute{$k}."\n";
    }
  }
  print "    type: depute\n";
  exit;
}

print "{";
foreach $k (keys %depute) {
  if (ref($depute{$k}) =~ /HASH/) {
    print '"'.lc($k).'": ['.join(", ", map { s/"/\\"/g; '"'.$_.'"' } keys %{$depute{$k}})."], ";
  } else {
    $depute{$k} =~ s/"/\\"/g;
    print '"'.lc($k).'": "'.$depute{$k}.'", ';
  }
}
print '"type": "depute"}'."\n";
