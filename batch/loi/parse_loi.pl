#!/usr/bin/perl

$file = $source = shift;

$source =~ s/^[^\/]+\///;
$source =~ s/html\///;
$source =~ s/http(.?)-/http\1:/;
$source =~ s/_/\//g;
$loi = $source;
$loi =~ s/^http\:\/\/.*r0*(\d+)-a0\.asp$/\1/;
$loi =~ s/^http\:\/\/.*\/(pl|pion)0*(\d+)\.asp$/\2/;
$loi =~ s/^http\:\/\/.*\/ta\/ta0*(\d+)\.asp$/ta\1/;
if ($loi =~ /ta/ || $source =~ /rapports.*-a0/i) {
  $present = 0;
} else {
  $present = 0;
}
use HTML::Entities;

open(FILE, $file) ;
@string = <FILE>;
$string = "@string";
$string =~ s/<br>\s*\n//gi;
$string =~ s/&nbsp;/ /gi;
$string =~ s/&#339;|œ+/oe/g;
$string =~ s/&#8217;/'/g;
$string =~ s/&#8211;/-/g;
$string =~ s/&#8230;/\.\.\./g;
$string =~ s/<\/?u>//gi;
$string =~ s/<\/?sup>//gi;
$string =~ s/<\/?span( style=[^>]+)?>//gi;
$string =~ s/<!\-\-\w*\-\->//ig;
$string =~ s/<a name="[^"]*">[^<]*<\/a>//gi;
$string =~ s/\s*<[a-z]+>\s*\(nouveau\)\s*<\/[a-z]+>//gi;
$string =~ s/\s*\(nouveau\)//gi;
$string =~ s/\r//g;
$string =~ s/\|(\W+)\|/$1/g;
close FILE;

if ($string =~ /adopt.+\spar\sle\ss.+nat/i) {
  $present = 0;
} 

sub checkout_loi {
  $expose =~ s/<p>\s*<a href/<p>&nbsp;<a href/i;
  $expose =~ s/<ul>/<\/p><ul>/gi;
  $expose =~ s/<\/ul><\/p>/<\/ul>/gi;
  print '{"type": "loi", "loi": "'.$loi.'", "titre": "'.$titreloi.'", "expose": "'.$expose.'", "auteur": "'.$auteur.'", "date": "'.$date.'", "source": "'.$source."\"}\n";
  $expose = "";
}

$accents = '[ÀÉÈÊËÎÏÔÙÛÜÇ]';
$upcases = "([A-Z]|$accents)";
sub name_lowerize {
  my $name = shift;
  utf8::decode($name);
  $name = decode_entities($name);
  $name =~ s/ ($upcases\w{1,4}+ )/ \L$1/g;
  $name =~ s/ ($upcases\w{1,4}+ )/ \L$1/g;
  $name =~ s/($upcases')/\L$1/g;
  $name =~ s/$upcases(\w+ ?)/$1\L$2/g;
  $name =~ s/(([^' ]|$accents))(\w+)/$1\L$3/g;
  utf8::encode($name);
  $name =~ s/\s+/ /g;
  return ucfirst($name);
}  

sub checkout_level {
  $level = shift;
  if ($level == 0) {
    return;
  }
  if ($present == 0 && $level == 1 && $levels[0] == 1) {
    checkout_loi();
  }
  if ($levels[$level-1] != 0) {
    print '{"type": "section", "loi": "'.$loi.'", "level": "'.$level.'", "leveltype": "'.$leveltype.'", "level1": "'.$levels[0].'", "level2": "'.$levels[1].'", "level3": "'.$levels[2].'", "level4": "'.$levels[3].'", "titre": "'.name_lowerize($titre).'", "expose": "'.$exposelevels[$level-1]."\"}\n";
  }
  for ($i = $level - 1; $i < 4; $i++) {
    $exposelevels[$i] = "";
  }
  $exposearticle = "";
  $titre = "";
}

sub checkout_present_article {
  while ($exposearticle =~ /<a href=["'][^"']*;[^"']*["']>/i) {
    $exposearticle =~ s/<a href=["']([^"']*);([^"']*)["']>/<a href='\1.\2'>/gi;
  }
  if ($levels[0] != 0 && $num_article != 0) {
   if (!($exposearticle =~ /^$/)) {
#    $exposearticle =~ s/\s*$/<\/p>/;
    if ($num_article == 1) {
      $num_article_titre = "1er";
    } else {
      $num_article_titre = $num_article;
    }
    print '{"type": "article", "loi": "'.$loi.'", "level": "'.$level.'", "leveltype": "'.$leveltype.'", "level1": "'.$levels[0].'", "level2": "'.$levels[1].'", "level3": "'.$levels[2].'", "level4": "'.$levels[3].'", "article": "'.$num_article_titre.'", "ordre": "", "expose": "'.$exposearticle."\"}\n";
   } $exposearticle = "";
  }
}

sub checkout_article {
  if ($num_article != 0 && $titre_article != '') {
    print '{"type": "article", "loi": "'.$loi.'", "level1": "'.$levels[0].'", "level2": "'.$levels[1].'", "level3": "'.$levels[2].'", "level4": "'.$levels[3].'", "article": "'.$titre_article.'", "ordre": "'.$num_article.'", "expose": ""}'."\n";
  }
}

sub checkout_alinea {
  print '{"type": "alinea", "loi": "'.$loi.'", "level1": "'.$levels[0].'", "level2": "'.$levels[1].'", "level3": "'.$levels[2].'", "level4": "'.$levels[3].'", "article": "'.$titre_article.'", "alinea": "'.$num_alinea.'", "texte": "'.$texte."\"}\n";
  $texte = "";
}

# Convert from roman numbers
%romans_map = ('M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400, 'C' => 100, 'XC' => 90, 'L' => 50, 'XL' => 40, 'X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1);
sub romans {
  $num = shift;
  $res = 0;
  while (($r, $d) = each(%romans_map)) {
    while ($num =~ s/^$r//i) {
     $res += $d;
    }
  }
  $res += $num;
  return $res;
}

sub set_level {
  $leveltype = lc(shift);
  $levelvalue = romans(shift);
  $more = shift || "";
  $more =~ s/<[^>]*>//g;
  $oldlevel = $curlevel;
  if (!$hierarchy{$leveltype}) {
    $curlevel += 1;
    $hierarchy{$leveltype} = $curlevel;
  } else {
    $curlevel = $hierarchy{$leveltype};
  }
  $levels[$curlevel-1] = $levelvalue.$more;
  for ($i=$curlevel; $i<4; $i++) {
    $levels[$i] = 0;
  }
  #print "TEST $leveltype ; $levelvalue.$more ; $curlevel ; $hierarchy ; $levels\n";
}

sub handle_text {
  if ($deftitre == 0) {
    if ($content =~ /^\s*((chap|t)itre|volume|livre|tome|(sous-)?section)\s+(\d+|[ivx]+)(e?r?\s*(<i>\s*)?(un|duo|tre)?(bis|qua|quint|quinqu|sex|oct|nov|non|dec)?(ter|ies)?)/i) {
      set_level($1, $4, $5);
      $deftitre = 1;
    } elsif ($align =~ /center/ && $content =~ /<b>\s*Article/) {
      $content_art = $content;
      $content_art =~ s/<\/?(i|em)>//gi;
      $content_art =~ s/<\/b>\s+<b>/ /gi;
      if ($content_art =~ /<b>\s*Article\s+(\d+)(er|\s+)?(un|duo|tre|)?(bis|qua|quint|quinqu|sex|oct|nov|non|dec)?(ter|ies)?(\s*[A-Z]+)?/i) {
        $titre_article = $1.$2.$3.$4.$5.$6;
        $titre_article =~ s/\s+$//;
        $num_alinea = 0;
        $texte = "";
      }
    } elsif (!($titre_article =~ /^$/)) {
      if ($content =~ /(Fait|Délibéré\s+en\s+séance)(\s+publique)?,?\s+à\s+Paris,\s*le\s*\d+/) {
        exit;
      }
      if (!($content =~ /^(<[a-z]+>)?\[?\(?(non\smodifié|suppressions?\s+(conform|maintenu)es?|supprimés?|dispositions?\s+déclarées?\s+irrecevables?\s+au\s+regard\s+de\s+l'article\s+\d+\s+de\s+la\s+constitution|division)(\s+et\s+intitulé)?(\s+nouveaux|nouvelle)?\)?\]?(<\/[a-z]+>)?/i)) {

        if ($num_alinea == 0) {
          $num_article++;
          checkout_article();
        }
        $num_alinea++;
        $texte = '<p>'.$content.'</p>';
        checkout_alinea();
      }
    }
  } else {
    $content =~ s/<\/?[a-z]+>//ig;
    $titre = $content;
    checkout_level($curlevel);
    $deftitre = 0;
  }
}

sub reset_vars {
  $levels = [0,0,0,0];
  $exposelevels = ["","","",""];
  $curlevel = 0;
  $leveltype = "";
  $article = 0;
  $num_article = 0;
  $alinea = 0;
  $texte = "";
  $expose = "";
  $exposearticle = "";
  $titre = "";
  $titrearticle = "";
  $hierarchy = {};
  $deftitre = 0;
}

$auteur = "";
$zone = 0;
reset_vars();

foreach $line (split /\n/, $string) {
#   print $line."\n";
  if ($line =~ /<meta name=/i) {
    if ($line =~ /name="DATE_DEPOT"/) {
      $line =~ s/^.*content="([^"]+)".*$/\1/;
      if ($line =~ /(\d{1,2})\/(\d{2})\/(\d{4})/) {
        $date = $3.'-'.$2.'-'.sprintf('%02d', $1);
      }
    } elsif ($line =~ /name="AUTEUR"/) {
      $line =~ s/^.*content="([^"]+)".*$/\1/;
      $line =~ s/^\s*(.*)\s+auteur.*$/\1/i;
      $line =~ s/\s$//;
      if (!($line =~ /ministre/i)) {
        $auteur = $line;
      }
    } elsif ($line =~ /name="TITRE_DOSSIER"/) {
      $line =~ s/^.*content="([^"]+)".*$/\1/;
      $titreloi = $line;
    }
  } elsif ($present == 1 && $line =~ /<p style="text-align: (center|justify)">(.*)<\/p>/) {
    $align = $1;
    $content = $2;

    if ($content =~ /(PRO.*DE\s+LOI|EXPOS.*MOTIF)/) {
      if ($zone == 2) {
        if ($levels[0] == 0) {
          checkout_loi();
          $titre = "";
        }
      }
      reset_vars();
      $zone++;
      next;
    }
    
    if ($zone == 2) {

      if ($content =~ /(\*\*\*|<b>.*((chap|t)itre)\s+(premier|[ivxIVX]+[eE]?[rR]?).*<\/b>)/) {
        checkout_present_article();
        for ($i == 4; $i > 0; $i++) {
          checkout_level($i);
        }
        $check = 1;
        if ($content =~ /\*\*\*/) {
          next;
        }
      }
      if ($check == 1 && $content =~ /((chap|t)itre|volume|livre|tome)\s+(\d+|premi|[ivx]+)e?r?,?(<|\s+)/) {
        if ($levels[0] == 0) {
          checkout_loi();
        }
        if ($levels[$curlevel-1] != 0) {
          checkout_present_article();
          checkout_level($curlevel);
        }
        set_level($1, $3);
        $exposelevels[$curlevel-1] .= '<p>'.$content.'</p>';
        $check = 0;
      } elsif ($content =~ /la(\s+|\s*<[a-z]*>\s*)?((sous-)?section)\s+(\d+|[ivx]+)/i) {
        if ($levels[$curlevel-1] != 0) {
          checkout_present_article();
          checkout_level($curlevel);
        }
        set_level($2, $4);
      }

      if ($levels[0] == 0) {
        $expose .= '<p>'.$content.'</p>';
      } else {
        if ($content =~ /<b>.*article.*<\/b>/i) {
          $content =~ s/M\./M /g;
          $texteassemble = '';
          while ($content =~ /<(a\s+href=["'][^"']*\.[^"']*["'])>/i) {
            $content =~ s/<a\s+href=["']([^"']*)\.([^"']*)["']>/<a href='\1;\2'>/gi;
          }
          foreach $phrase (split /\.\s*/, $content) {
            if ($phrase =~ /la\s+((sous-)?section)\s+(\d+|[ivx]+)/i) {
              set_level($1, $3);
              $exposelevels[$curlevel-1] = '<p>'.$phrase.'.</p>';
              if (!($phrase =~ /article\s+(\d+)/i)) {
                next;
              }
            }
            if ($phrase =~ /<b>.*articles?\s+(\d+).*<\/b>/i && $num_article + 1 == $1) {
              checkout_present_article();
              $num_article = $1;
            }
            if ($num_article != 0) {
              $texteassemble .= $phrase.'. ';
            }
          }
          if (!$texteassemble =~ /^$/) {
            $exposearticle .= '<p>'.$texteassemble.'</p>';
          }
  #        if (!($exposearticle =~ /article\s+($num_article)/)) {
  #          if ($section != 0 && !($exposesection =~ /$content/)) {
  #            $exposesection .= '<p>'.$content.'</p>';
  #          }
  #          elsif (!($exposechapitre =~ /$content/)) {
  #            $exposechapitre .= '<p>'.$content.'</p>';
  #          }
  #        }
        } elsif ($levels[$curlevel-1] != 0 && !($exposelevels[$curlevel-1] =~ /$content/)) {
          $exposelevels[$curlevel-1] .= '<p>'.$content.'</p>';
#        } elsif (!($exposearticle =~ /article\s+($num_article)/) && !($exposechapitre =~ /$content/)) {
#          $exposechapitre .= '<p>'.$content.'</p>';
        } elsif (!$exposearticle =~ /^$/) {
          $exposearticle .= '<p>'.$content.'</p>';
        }
      }
    } elsif ($zone == 3) {
      handle_text();
    }
  } elsif ($present == 0) {
    if ($line =~ /<p style="text-align: (center|justify)">(.*)<\/p>/) {
      $align = $1;
      $content = $2;
      handle_text();
    } elsif ($line =~ /<p>(.*)<\/p>/) {
      $align = 'none';
      $content = $1;
      handle_text();
    }
  }
}

