#!/usr/bin/perl

$file = $source = shift;
$titre_chap = shift;

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
  $present = 1;
}

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

sub checkout_chapitre {
  if ($present == 0 && $chapitre == 1) {
    checkout_loi();
  }
  if ($chapitre != 0) {
    print '{"type": "chapitre", "loi": "'.$loi.'", "chapitre": "'.$chapitre.'", "titre": "'.$titre.'", "expose": "'.$exposechapitre."\"}\n";
  }
  $exposechapitre = "";
  $exposesection = "";
  $exposearticle = "";
  $titre = "";
  $section = 0;
}

sub checkout_section {
  if ($section != 0) {
    print '{"type": "section", "loi": "'.$loi.'", "chapitre": "'.$chapitre.'", "section": "'.$section.'", "titre": "'.$titre.'", "expose": "'.$exposesection."\"}\n";
  }
  $exposesection = "";
  $exposearticle = "";
  $titre = "";
}

sub checkout_present_article {
  while ($exposearticle =~ /<a href=["'][^"']*;[^"']*["']>/i) {
    $exposearticle =~ s/<a href=["']([^"']*);([^"']*)["']>/<a href='\1.\2'>/gi;
  }
  if ($chapitre != 0 && $num_article != 0) { if (!(($loi == 1890 && ($chapitre == 5 || $num_article == 101)) || ($loi == 1697 && ($chapitre == 3 && $section >= 2 && $num_article == 9) ))) {
   if (!($exposearticle =~ /^$/)) {
#    $exposearticle =~ s/\s*$/<\/p>/;
    if ($num_article == 1) {
      $num_article_titre = "1er";
    } else {
      $num_article_titre = $num_article;
    }
    print '{"type": "article", "loi": "'.$loi.'", "chapitre": "'.$chapitre.'", "section": "'.$section.'", "article": "'.$num_article_titre.'", "ordre": "", "expose": "'.$exposearticle."\"}\n";
   } } $exposearticle = "";
  }
}

sub checkout_article {
  if ($num_article != 0 && $titre_article != '') {
    print '{"type": "article", "loi": "'.$loi.'", "chapitre": "'.$chapitre.'", "section": "'.$section.'", "article": "'.$titre_article.'", "ordre": "'.$num_article.'", "expose": ""}'."\n";
  }
}

sub checkout_alinea {
  print '{"type": "alinea", "loi": "'.$loi.'", "chapitre": "'.$chapitre.'", "section": "'.$section.'", "article": "'.$titre_article.'", "alinea": "'.$num_alinea.'", "texte": "'.$texte."\"}\n";
  $texte = "";
}

sub handle_text {
  if ($deftitre =~ /^none$/) {
    if (($titre_chap =~ /^titre$/ && $content =~ /^\s*(T|t)(itre|ITRE)\s+(\d+|[IVX]+)(ER|er)?/) || (!($titre_chap =~ /^titre$/) && $content =~ /^\s*(T|CHAP|Chap)(itre|ITRE)\s+(\d+|[IVX]+)(ER|er)?/)) {
      $chapitre++;
      $section = 0;
      $deftitre = 'chapitre';
    } elsif (($titre_chap =~ /^titre$/ && $content =~ /^\s*(C|c)(HAPITRE|hapitre)\s+(\d+|[IVX]+)(ER|er)?/) || (!($titre_chap =~ /^titre$/) && $content =~ /^\s*section\s+\d+/i)) {
      $section++;
      $deftitre = 'section';
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
    if ($deftitre =~ /^chapitre$/) {
      checkout_chapitre();
    } elsif ($deftitre =~ /^section$/) {
      checkout_section();
    }
    $deftitre = 'none';
  }
}

sub reset_vars {
  $chapitre = 0;
  $section = 0;
  $article = 0;
  $num_article = 0;
  $alinea = 0;
  $texte = "";
  $expose = "";
  $exposechapitre = "";
  $exposesection = "";
  $exposearticle = "";
  $titre = "";
  $titrearticle = "";
}

$auteur = "";
$zone = 0;
$deftitre = 'none';
reset_vars();
if ($loi =~ /ta376/) {
  $date = "2009-12-02";
  $auteur = "M. Jean-Luc Warsmann";
}

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
        if ($chapitre == 0) {
          checkout_loi();
          $titre = "";
        } elsif ($loi == 1697) {
          while ($num_article < 47) {
            $tmp_expose = $exposearticle;
            checkout_present_article();
            $num_article ++;
            $exposearticle = $tmp_expose;
          }
        } elsif ($loi == 2760) {
          checkout_present_article();
          checkout_chapitre();
        }
      }
      reset_vars();
      $zone++;
      next;
    }
    
    if ($zone == 2) {

      if ($content =~ /(\*\*\*|<b>.*(chap|t)itre\s+(premier|[IVX]+[eE]?[rR]?).*<\/b>)/) {
        checkout_present_article();
        checkout_section();
        checkout_chapitre();
        $check = 1;
        if ($content =~ /\*\*\*/) {
          next;
        }
      }
      if ($check == 1 && $content =~ /(chap|t)itre\s+(premier|[IVX]+[eE]?[rR]?),?(<|\s+)/) {
        if ($chapitre == 0) {
          checkout_loi();
        }
        $chapitre++;
        $exposechapitre .= '<p>'.$content.'</p>';
        $section = 0;
        $check = 0;
        if ($loi == 1890) {
          if ($num_article == 88) {
            $num_article = 101;
          } elsif ($num_article == 101) {
            $num_article = 135;
          }
        } elsif ($loi == 1697) {
          if ($num_article == 4) {
            $num_article = 8;
          } elsif ($num_article == 9) {
            $num_article = 18;
          }
        }
      } elsif ($content =~ /la(\s+|\s*<[a-z]*>\s*)?section\s+(\d+)/i && ($section + 1 == $2)) {
        if ($section != 0) {
          checkout_present_article();
          checkout_section();
        }
       # $exposesection = '<p>'.$content.'</p>';
        $section = $2;
      }

      if ($chapitre == 0) {
        $expose .= '<p>'.$content.'</p>';
      } else {
        if ($content =~ /<b>.*article.*<\/b>/i) {
          $content =~ s/M\./M /g;
          $texteassemble = '';
          while ($content =~ /<(a\s+href=["'][^"']*\.[^"']*["'])>/i) {
            $content =~ s/<a\s+href=["']([^"']*)\.([^"']*)["']>/<a href='\1;\2'>/gi;
          }
          foreach $phrase (split /\.\s*/, $content) {
            if ($phrase =~ /la\s+section\s+(\d+)/i) {
              $exposesection = '<p>'.$phrase.'.</p>';
              if (!($phrase =~ /article\s+(\d+)/i)) {
                next;
              }
            }
            if ($phrase =~ /<b>.*articles?\s+(\d+).*<\/b>/i && (($num_article + 1 == $1) || ($loi == 2760 && $num_article == 6))) {
              checkout_present_article();
              $num_article = $1;
              if ($loi == 2760 && $num_article == 6) {
                $num_article = 7;
              }
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
        } elsif ($section != 0 && !($exposesection =~ /$content/)) {
          $exposesection .= '<p>'.$content.'</p>';
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

