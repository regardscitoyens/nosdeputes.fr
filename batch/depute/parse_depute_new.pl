#!/usr/bin/perl

use HTML::TokeParser;
use HTML::Entities;
use Encode;
require "finmandats.pm";
require "../common/common.pm";

$file = shift;
$yml = shift || 0;
$display_text = shift;

open(FILE, $file);
@string = <FILE>;
$string = "@string";
close FILE;
$string =~ s/\r//g;
$string =~ s/[\n\s]+/ /g;
$string =~ s/^.*(<h1 class="deputy-headline-title)/\1/i;
$string =~ s/<div id="actualite".*<\/div>(<div id="fonctions")/\1/i;
$string =~ s/<div id="travaux".*$//i;
while ($string =~ s/(<li class="contact-adresse">([^<]*)?)(<\/?p>)+(.*<\/li>(<li class="contact-adresse">|<\/ul>))/\1 \4/gi) {}
$string =~ s/(<(div|p|li|abbr|img|a|dt)[ >])/\n\1/ig;
$string =~ s/\s*'\s*/'/g;

if ($display_text) {
  print $string;
  exit;
}

my %depute;

sub clean_vars {
  $encours = $lieu = $organisme = $fonction = "";
}

sub add_mandat {
  $start = shift; 
  $end = shift;
  $cause = shift;
  if ($cause =~ /(remplacement.*)\s*:\s*(.*)\s*$/i) {
    $depute{'suppleant_de'} = $2;
    $cause =~ s/\s*:\s*(.*)\s*$/ \(\1\)/;
  }
  $depute{'premiers_mandats'}{"$start / $end / ".lc($cause)} = 1;
  $depute{'debut_mandat'} = max_date($start,$depute{'debut_mandat'});
  $depute{'fin_mandat'} = max_date($end,$depute{'fin_mandat'}) if ($end !~ /^$/ && max_date($end,"20/06/2007") != "20/06/2007");
}

if ($file =~ /(\d+)/) {
  $depute{'id_institution'} = $1;
  $depute{'url_institution'} = "http://www.assemblee-nationale.fr/13/tribun/fiches_id/$1.asp";
  $depute{'fin_mandat'} = $fin_mandat{"$1.asp"};
  $depute{'photo'} = "http://www.assemblee-nationale.fr/13/tribun/photos/$1.jpg";
}

$read = "";
foreach $line (split /\n/, $string) {
  if ($line =~ /<h1 class="deputy-headline-title[^>]*>(.+)<\/h1>/i) {
    $depute{'nom'} = $1;
    $depute{'nom'} =~ s/,.*$//;
    $depute{'nom'} =~ s/^(M[.mle]+) //;
    if ($1 =~ /e/) {
      $depute{'sexe'} = "F";
    } else {
      $depute{'sexe'} = "H";
    }
  } elsif (!$depute{'circonscription'} && $line =~ />([^<]*) \((\d+[èrme]+)( circonscription)?\)</i) {
    $depute{'circonscription'} = "$1 ($2)";
  } elsif ($line =~ /Née? le ([0-9]*e?r? \S* [0-9]*)( [àau]+ (.*))?</i) {
    $depute{'date_naissance'} = join '/', reverse datize($1);
    $depute{'lieu_naissance'} = $3 if ($3 !~ /\(\)/);
    $read = "profession";
  } elsif ($line =~ /title="Suppléant"/i) {
    $read = "suppleant";
  } elsif ($read !~ /^$/) {
    $depute{"$read"} = $1 if ($line =~ />([^<]+)</);
    if ($read =~ /suppleant/) {
      $depute{"$read"} =~ s/[(,\s]+décédé.*$//i;
      $depute{"$read"} =~ s/Mlle /Mme /;
    }
    $read = "";
  } elsif ($line =~ /class="political-party[^>]*>([^<]+)</i) {
    $groupe = lc($1);
    if ($groupe =~ s/^(apparentée?|présidente?)( du groupe)? //) {
      $depute{'groupe'} = $groupe." / ".$1;
    } else {
      $depute{'groupe'} = $groupe." / membre";
    }
  } elsif ($line =~ /img [^>]*class="deputy-profile-picture[^>]* src="([^"]+)"/i) {
    $depute{'photo'} = "http://www.assemblee-nationale.fr$1";
  } elsif ($line =~ /mailto:([^'"]+)['"]/i) {
    $depute{'mails'}{$1} = 1;
  } elsif ($line =~ /<a [^>]*href=['"]([^"']+)['"].*_blank/i) {
    $depute{'sites_web'}{$1} = 1;
  } elsif ($line =~ /li class="contact-adresse">\s*([^\/]*)\s*<\/li>/i) {
    $depute{'adresses'}{$1} = 1;
  } elsif ($line =~ /"hemicycle-picture".*place occupée[\s:]+(\d+)[\s"]/i) {
    $depute{'place_hemicycle'} = $1;
  } elsif ($line =~ /\(Date de début de mandat[\s:]+([\d\/]+)( \((.*)\)\))?/i) {
    add_mandat($1,"",$3);
  } elsif ($line =~ /Mandat du ([\d\/]+)( \(.*\))? au ([\d\/]+)( \((.*)\))?/i) {
    add_mandat($1,$3,$5);
  } elsif ($line =~ /class="article-title/) {
    clean_vars();
    $line =~ s/\s*<[^>]+>\s*//g;
    if ($line =~ /(Bureau|Commissions?|Missions? (temporaire|d'information)s?|Délégations? et Offices?)/) {
      $encours = "fonctions";
    } elsif ($line =~ /(Organismes? extra-parlementaires?|Fonctions? dans les instances internationales ou judiciaires)/) {
      $encours = "extras"; 
    } elsif ($line =~ /(Mandats? loca[lux]+ en cours|Mandats? intercommuna)/i) {
      $encours = "autresmandats"; 
    } elsif ($line =~ /^Anciens mandats/i && $line !~ /Assemblée nationale/i) {
      $encours = "anciensmandats"; 
    } elsif ($line =~ /(Groupes? d'études?|Groupes? d'amitié)/i) {
      $encours = "groupes";
      $type_groupe = $line;
    }
  } elsif ($line =~ /<div id="/i) {
    clean_vars();
  } elsif ($encours !~ /^$/) {
    $line =~ s/\s*<[^>]+>\s*//g;
    next if ($line =~ /^$/);
    if ($encours =~ /anciensmandats/) {
      if ($line =~ /du (\d+\/\d+\/\d+) au (\d+\/\d+\/\d+) \((.*)\)/i) {
        $dates = "$1 / $2";
        $fonction = $3;
        $tmporga = lc($organisme);
        $tmporga =~ s/\W/./g;
        $fonction =~ s/\s*(d[elau'\s]+)?$tmporga\s*//i;
        $depute{$encours}{"$lieu / $organisme / $fonction / $dates"} = 1;
      } elsif ($line =~ /^\s*(.[^A-Z\(]+) d(e la |[ue]s? |'|e l')([A-ZÀÉÈÊËÎÏÔÙÛÇ].*)$/) {
#      } elsif ($line =~ /^\s*(.[^(A-ZÀÉÈÊËÎÏÔÙÛÇ]*) d([ue](s| la)? |'|e l')(\U.*)$/) {
        $organisme = $1;
        $lieu = $3;
        $organisme = "Conseil de Paris" if ($lieu =~ s/ \(Département de Paris\)/ (Département)/);
      } else {
        $lieu = $line;
        if ($line =~ /^\s*c(ommunauté d[elau'\s]+\S+) (d[elasu'\s]+)?(\U.*)$/i) {
          $organisme = "C$1";
          $lieu = $3;
        } else {
          $organisme = "Communauté d'agglomération";
        }
      }
    } elsif ($encours =~ /autresmandats/) {
      if ($line =~ /^\s*(.*) (de la )?c(ommunauté d[elau'\s]+\S+) (d[elsau'\s]+)?(\U.*)$/i) {
        $organisme = "C$3";
        $lieu = $5;
        $fonction = $1;
      } else {
        $lieu = "";
        if ($line =~ s/^(.*) d([ue](s| la)? |'|e l')([A-ZÀÉÈÊËÎÏÔÙÛÇ].*)$/\1/) {
          $lieu = $4;
        } elsif ($line =~ s/^(.*)\(([A-ZÀÉÈÊËÎÏÔÙÛÇ].*)\)$/\1/) {
          $lieu = $2;
        }
        $line =~ s/\s+$//;
        $organisme = ucfirst($4) if ($line =~ s/^(.*) d((u|e la) |e l')(.*)$/\1/);
        $fonction = $line;
        $organisme = "Conseil municipal" if ($fonction =~ /Maire/i);
      }
      $lieu =~ s/, (.*)$/ (\1)/;
      $depute{$encours}{"$lieu / $organisme / $fonction"} = 1;
    } elsif ($encours =~ /groupes/ && $line =~ s/^\s*(.*) : - //) {
      $fonction = $1;
      $type = "Groupe d'amitié France-";
      $type = "Groupe d'études " if ($type_groupe =~ /étude/i);
      foreach $gpe (split / - /, $line) {
        $depute{$encours}{"$type$gpe / $fonction"} = 1;
      }
    } else {
      if ($line =~ /^\s*(.*) \((.*) - mission débutée.*\)/i) {
        $organisme = "Mission temporaire pour le $2 : $1";
        $fonction = "Chargé".($depute{'sexe'} eq "F" ? "e" : "")." de mission";
      } else {
        $line =~ s/ (\(ex|depuis le) .*$//;
        $fonction = $1 if ($line =~ s/^\s*((\S+\s*){1,3}( du bureau)?) d((u|e la) |e l')(.*)$/\6/);
        $organisme = ucfirst($line);
        $organisme =~ s/^(Assemblée nationale)/Bureau de l'\1/i;
      }
      $depute{$encours}{"$organisme / $fonction"} = 1;
    }
  }
}


#TODO :
# - change id_an et url_an dans update:Deputes
# - fix changements nom_famille
# - multiples sites_web, facebook? tiwtter?
# - find suppléant si existe
# - add gestion mission tempo
# - gérer stockage anciens mandats, premiers_mandats, groupes...

#On récupère le nom de famille à partir des emails
$nomdep = $depute{'nom'};
@noms = split / /, $nomdep;
if ((join " ", keys %{$depute{'mails'}}) =~ /(\S+)\@assemblee/) {
    $login = $1;
    while ($login = substr($login, 1)) {
        for($i = 0 ; $i <= $#noms ; $i++) {
            $tmpnom = $noms[$i];
            $tmpnom =~ s/[àÀéÉèÈêÊëËîÎïÏôÔùÙûÛçÇ]/./ig;
            if ($login =~ /$tmpnom/i)  {
                if ($nomdep =~ /(\s[dl][ea]s?\s)?(\S*$login.*$)/i) {
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
$depute{'nom_de_famille'} =~ s/^\s+//; 
$depute{'nom_de_famille'} =~ s/\s+$//;


if ($yml) {
    print "  depute_".$depute{'id_an'}.":\n";
    foreach $k (keys %depute) {
        if (ref($depute{$k}) =~ /HASH/) {
            print "    ".lc($k).":\n";
            foreach $i (keys %{$depute{$k}}) {
                print "      - $i\n";
            }
        }else {
            print "    ".lc($k).": ".$depute{$k}."\n";
        }
    }
    print "    type: depute\n";
    exit;
}

print "{ ";
foreach $k (keys %depute) {
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


