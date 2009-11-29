#!/usr/bin/perl

$file = $source = shift;
$source =~ s/^[^\/]+\///;
$source =~ s/html\///;
$source =~ s/_/\//g;
$loi = $source;
$loi =~ s/^http\:\/\/.*[a-z](\d+)\.asp$/\1/;

open(FILE, $file) ;
@string = <FILE>;
$string = "@string";
$string =~ s/<\/?b>/|/g;
$string =~ s/<\/?i>/\//g;
$string =~ s/\r//g;
$string =~ s/&nbsp;/ /g;
$string =~ s/&#339;|œ+/oe/g;
$string =~ s/\|(\W+)\|/$1/g;
$string =~ s/<br>\n//gi;
#$string =~ s/\<br\>.*\n//g;
$string =~ s/&#8217;/'/g;
$string =~ s/&#8211;/-/g;
$string =~ s/<span style=[^>]+>([^<]+)<\/span>/\1/gi;
$string =~ s/<a name="[^"]*">[^<]*<\/a>//gi;
close FILE;

sub checkout_loi {
  print '{"type": "loi", "loi": "'.$loi.'", "titre": "'.$titreloi.'", "expose": "'.$expose.'", "auteur": "'.$auteur.'", "date": "'.$date.'", "source": "'.$source."\"}\n";
  $expose = "";
  $titre = "";
}

sub checkout_chapitre {
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

sub checkout_article {
  if ($chapitre != 0 && $num_article != 0) { if ($chapitre != 5 && $num_article != 101) { if (!($exposearticle =~ /^$/)) {
    $exposearticle =~ s/\s*$/<\/p>/;
    print '{"type": "article", "loi": "'.$loi.'", "chapitre": "'.$chapitre.'", "section": "'.$section.'", "article": "'.$num_article.'", "expose": "'.$exposearticle."\"}\n";
    } } $exposearticle = "";
  }
}

sub checkout_alinea {
  print '{"type": "alinea", "loi": "'.$loi.'", "chapitre": "'.$chapitre.'", "section": "'.$section.'", "article": "'.$num_article.'", "alinea": "'.$num_alinea.'", "texte": "'.$texte.'", "ref_loi": "'.$ref_loi.'", "ref_art": "'.$ref_art."\"}\n";
  $texte = "";
}

sub reset_vars {
  $chapitre = 0;
  $section = 0;
  $article = 0;
  $alinea = 0;
  $texte = "";
  $expose = "";
  $exposechapitre = "";
  $exposesection = "";
  $exposearticle = "";
  $titre = "";
}

$zone = 0;
$deftitre = 'none';
reset_vars();

foreach $line (split /\n/, $string) {
  # print $line."\n";
  if ($line =~ /<meta name=/i) {
    if ($line =~ /name="DATE_DEPOT"/) {
      $line =~ s/^.*content="([^"]+)".*$/\1/;
      if ($line =~ /(\d{1,2})\/(\d{2})\/(\d{4})/) {
        $date = $3.'-'.$2.'-'.sprintf('%02d', $1);
      }
    } elsif ($line =~ /name="AUTEUR"/) {
      $line =~ s/^.*content="([^"]+)".*$/\1/;
      $line =~ s/\s*auteur\s*//i;
      $auteur = $line;
    } elsif ($line =~ /name="TITRE_DOSSIER"/) {
      $line =~ s/^.*content="([^"]+)".*$/\1/;
      $titreloi = $line;
    }
  } elsif ($line =~ /<p style="text-align: (center|justify)">(.*)<\/p>/) {
    $align = $1;
    $content = $2;

    if ($content =~ /(PRO.*DE\s+LOI|EXPOS.*MOTIF)/) {
      reset_vars();
      $zone++;
      next;
    }
    
    if ($zone == 2) {

      if ($content =~ /\*\*\*/) {
        if ($chapitre == 0) {
          checkout_loi();
        }
        checkout_article();
        checkout_section();
        checkout_chapitre();
        $check = 1;
        next;
      }
      if ($check == 1 && $content =~ /chapitre\s+(premier|[IVX]+),?\s+/) {
        $chapitre++;
        if ($chapitre != 4 && $chapitre != 2) {
          $exposechapitre .= '<p>'.$content.'</p>';
        }
        $section = 0;
        $check = 0;
        if ($num_article == 88) {
          $num_article = 101;
        } elsif ($num_article == 101) {
          $num_article = 135;
        }
      } elsif ($content =~ /la\s+section\s+(\d+)/i && ($section + 1 == $1)) {
        if ($section != 0) {
          checkout_article();
          checkout_section();
        }
       # $exposesection = '<p>'.$content.'</p>';
        $section = $1;
      }

      if ($chapitre == 0) {
        $expose .= '<p>'.$content.'</p>';
      } else {
        if ($content =~ /article/i) {
          $content =~ s/M\./M /g;
          foreach $phrase (split /\.\s*/, $content) {
            if ($phrase =~ /la\s+section\s+(\d+)/i) {
              $exposesection = '<p>'.$phrase.'.</p>';
              if (!($phrase =~ /article\s+(\d+)/i)) {
                next;
              }
            }
            if ($phrase =~ /articles?\s+(\d+)/i && ($num_article + 1 == $1)) {
              checkout_article();
              $num_article = $1;
            }
            if ($num_article != 0) {
              if ($exposearticle =~ /^$/) {
                $exposearticle = '<p>';
              }
              $exposearticle .= $phrase.'. ';
            }
          }
          if (!($exposearticle =~ /article\s+($num_article)/)) {
            if ($section != 0 && !($exposesection =~ /$content/)) {
              $exposesection .= '<p>'.$content.'</p>';
            }
            elsif (!($exposechapitre =~ /$content/)) {
              $exposechapitre .= '<p>'.$content.'</p>';
            }
          }
        } elsif ($section != 0 && !($exposesection =~ /$content/)) {
          $exposesection .= '<p>'.$content.'</p>';
        } elsif (!($exposearticle =~ /article\s+($num_article)/) && !($exposechapitre =~ /$content/)) {
          $exposechapitre .= '<p>'.$content.'</p>';
        }
      }
    }

    elsif ($zone == 3) {
      $content =~ s/<sup>[a-z]+<\/sup>/\1/ig;
      if ($deftitre =~ /^none$/) {
        if ($content =~ /^Chapitre\s+[IVX]/) {
          $chapitre++;
          $section = 0;
          $deftitre = 'chapitre';
        } elsif ($content =~ /^Section\s+\d+/) {
          $section++;
          $deftitre = 'section';
        } elsif ($content =~ /\|\s*Article\s+(\d+)\s*\|/i) {
          $num_article = $1;
          $num_alinea = 0;
          $texte = "";
          $ref_loi = "";
          $ref_art = "";
        } else {
          $num_alinea++;
          $content =~ s/\/([^\/]+)\//<em>\1<\/em>/g;
          $content =~ s/\|([^\|]+)\|/<b>\1<\/b>/g;
          $texte = '<p>'.$content.'</p>';
          checkout_alinea();
        }
      } else {
        $content =~ s/\|//g;
        $titre = $content;
        if ($deftitre =~ /^chapitre$/) {
          checkout_chapitre(); 
        } elsif ($deftitre =~ /^section$/) {
          checkout_section();
        }
        $deftitre = 'none';
      }
    }
  }
}

