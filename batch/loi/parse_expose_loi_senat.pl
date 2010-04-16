#!/usr/bin/perl

$file = $source = shift;

$source =~ s/^[^\/]+\///;
$source =~ s/html\///;
$source =~ s/http(.?)-/http\1:/;
$source =~ s/_/\//g;
$loi = $source;
$loi =~ s/^http\:\/\/.*(senat).*[a-z](\d\d-\d{1,4})\.html$/$2/;
$present = 1;

open(FILE, $file) ;
@string = <FILE>;
$string = "@string";
$string =~ s/&Eacute;/É/g;
$string =~ s/&eacute;/é/g;
$string =~ s/&agrave;/à/g;
$string =~ s/<br>\s*\n//gi;
$string =~ s/<br\/?>/ /gi;
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
$string =~ s/([^p]>|[^>])\s*\n\s*([^<][^p\/])/\1 \2/g;
$string =~ s/\|(\W+)\|/$1/g;
$string =~ s/<[a-z]+>\s+<\/[a-z]+>/ /gi;
close FILE;

sub checkout_loi {
  $expose =~ s/<p>\s*<a href/<p>&nbsp;<a href/i;
  $expose =~ s/<ul>/<\/p><ul>/gi;
  $expose =~ s/<\/ul><\/p>/<\/ul>/gi;
  print '{"type": "loi", "loi": "'.$loi.'", "titre": "'.$titre_loi.'", "expose": "'.$expose.'", "auteur": "'.$auteur.'", "date": "'.$date.'", "source": "'.$source."\"}\n";
  $expose = "";
}

sub checkout_chapitre {
  if ($chapitre == 1) {
    checkout_loi();
  }
  if ($chapitre != 0) {
    print '{"type": "chapitre", "loi": "'.$loi.'", "chapitre": "'.$chapitre.'", "titre": "'.$titre_chapitre.'", "expose": "'.$expose."\"}\n";
  }
  $titre_chapitre = "";
  $titre_section = "";
}

sub checkout_section {
  if ($section != 0) {
    print '{"type": "section", "loi": "'.$loi.'", "chapitre": "'.$chapitre.'", "section": "'.$section.'", "titre": "'.$titre_section.'", "expose": "'.$expose."\"}\n";
  }
  $titre_section = "";
}

sub checkout_present_article {
  if ($num_article != 0 && !($expose =~ /^$/)) {
    while ($last_article < $num_article) {
      $last_article++;
      if ($last_article == 1) {
        $last_article_titre = "1er";
      } else {
        $last_article_titre = $last_article;
      }
      print '{"type": "article", "loi": "'.$loi.'", "chapitre": "'.$chapitre.'", "section": "'.$section.'", "article": "'.$last_article_titre.'", "ordre": "", "expose": "'.$expose."\"}\n";
    }
  }
}

sub checkout_expose {
  while ($expose =~ /<a href=["'][^"']*;[^"']*["']>/i) {
    $expose =~ s/<a href=["']([^"']*);([^"']*)["']>/<a href='\1.\2'>/gi;
  }
  if ($deftitre =~ /article/) {
    checkout_present_article();
  } elsif ($deftitre =~ /section/) {
    checkout_section();
  } elsif ($deftitre =~ /chapitre/) {
    checkout_chapitre();
  }
  $expose = "";
}

$zone = 0;
$auteur = "";
$read_auteur = 0;
$deftitre = '';
$chapitre = 0;
$section = 0;
$num_article = 0;
$last_article = 0;
$expose = "";
$titre_loi = "";
$titre_chapitre = "";
$titre_section = "";

foreach $line (split /\n/, $string) {
#   print $line."\n";
  if ($line =~ /<TITLE>(.*)<\/TITLE>/) {
    $titre_loi = $1;
  } elsif ($present == 1 && $line =~ /<p\sAlign="?(center|justify)"?>(.*)<\/p>/) {
    $align = $1;
    $content = $2;
    if ($align =~ /center/) {
      if ($read_auteur == 0 && $content =~ /PRÉSENTÉ/) {
        $read_auteur = 1;
      } elsif ($read_auteur == 1 && $content =~ /Par\sM[\.me]+\s([^,]*)/) {
        $auteur = $1;
        $read_auteur = 2;
      }
      if ($content =~ /(PRO.*DE\s+LOI|EXPOS.*MOTIF)/) {
        if ($zone == 2) {
          checkout_expose();
        }
        $zone++;
        next;
      }
    }
    if ($zone == 2) {
      if ($align =~ /center/) {
        if ($content =~ /<strong>\s*T(itre|ITRE)\s+(\d+|[IVX]+)(<\/?strong>)?(ER|er)?(<\/?strong>)?\.?(\s+-\s+)?(.*)\s*<\/strong>/) {
          checkout_expose();
          $deftitre = 'chapitre';
          $chapitre++;
          $section = 0;
          $titre_chapitre = $7;
        } elsif ($content =~ /<strong>\s*C(hapitre|HAPITRE)\s+(\d+|[IVX]+)(<\/?strong>)?(ER|er)?(<\/?strong>)?\.?(\s+-\s+)?(.*)\s*<\/strong>/) {
          checkout_expose();
          $section++;
          $deftitre = 'section';
          $titre_section = $7;
        } elsif ($content =~ /<strong>\s*Articles?\s+(\d+\s+)?(à|et)?\s*(\d+)(ER|er)?\s*<\/strong>/) {
          checkout_expose();
          $last_article = $num_article;
          $num_article = $3;
          if ($1 =~ /(\d+)/) {
            $last_article = $1;
            $last_article--;
          }
          $deftitre = 'article';
        }
      } else {
        $expose .= '<p>'.$content.'</p>';
      }
    }
  }
}

