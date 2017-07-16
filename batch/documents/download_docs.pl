#!/usr/bin/perl

use WWW::Mechanize;
use HTML::TokeParser;

$legislature = shift || 14;
$lastyear = localtime(time);
$lastyear =~ s/^.*\s(\d{4})$/$1/;
$lastyear++;

$a = WWW::Mechanize->new(autocheck => 0);

#@urls = ("http://www.assemblee-nationale.fr/$legislature/documents/index-depots.asp",
@urls = ("http://www2.assemblee-nationale.fr/documents/liste/%28type%29/depots",
         #"http://www.assemblee-nationale.fr/$legislature/documents/index-rapports.asp",
         "http://www2.assemblee-nationale.fr/documents/liste/%28type%29/rapports",
         #"http://www.assemblee-nationale.fr/$legislature/documents/index-application_lois.asp",
         "http://www2.assemblee-nationale.fr/documents/liste/%28type%29/rapports-application-loi",
         #"http://www.assemblee-nationale.fr/$legislature/europe/index-rapinfo.asp",
         #"http://www2.assemblee-nationale.fr/14/autres-commissions/commission-des-affaires-europeennes/%28block%29/RapportsInfoParlementairesInstance/%28init%29/0", // JS LOAD
         "http://www.assemblee-nationale.fr/$legislature/documents/index-information-comper.asp",
         #"http://www.assemblee-nationale.fr/$legislature/documents/index-rapports-legislation.asp",
         "http://www.assemblee-nationale.fr/$legislature/documents/index-oeps.asp",
         "http://www.assemblee-nationale.fr/documents/index-general-oecst.asp",
         "http://www.assemblee-nationale.fr/$legislature/documents/index-territoire.asp",
         "http://www.assemblee-nationale.fr/$legislature/documents/index-femmes.asp",
         "http://www.assemblee-nationale.fr/$legislature/documents/index-information-comper.asp",
         "http://www.assemblee-nationale.fr/$legislature/documents/index-enquete-rapports.asp"
);
for $year (2008 .. $lastyear) {
  @urls = (@urls, "http://www.assemblee-nationale.fr/$legislature/budget/plf$year/rapporteurs.asp");
}

foreach $baseurl (@urls) {
  $a->get($baseurl);
  $content = $a->content;
  $p = HTML::TokeParser->new(\$content);
  while ($t = $p->get_tag('a')) {
    $txt = $p->get_text('/a');
    $url = $t->[1]{href};
    if ($url =~ /^\//) {
      $url = "http://www.assemblee-nationale.fr".$url;
    }
    $url =~ s/\/documents\/notice//;
    $url =~ s/\/\(index\)\/[a-z]+$/.asp/;
    next if $url =~ /(dossiers|i0562.asp)/i;
    next if $url =~ /\.pdf$/i;
    next if $url =~ /\/documents\/index/i;
    next if $url =~ /\/budget\/redirect\//i;
    next if $url =~ /\/$legislature\/liste\//i;
    next if !($url =~ /nale\.fr\/$legislature\//);
    next if $url =~ /app\.(eu\.)?readspeaker\.com/i;
    $file = $url;
    $file =~ s/\//_/gi;
    $file =~ s/\#.*//;
    $type = "";
    if ($url =~ /(rap|budget)/i) {
      $type = "rap";
    } elsif ($url =~ /(resolutions|ppr)/i) {
      $type = "ppr";
    } elsif ($url =~ /(projets)/i) {
      $type = "pjl";
    } elsif ($url =~ /(propositions)/i) {
      $type = "ppl";
    } elsif ($url =~ /(ta-commission)/i) {
      $type = "ta";
    }
    if (-e "$type/$file") {
      system("grep -e 'pas encore ..\\?dit..d\\?\\|disponible en format <.*pdf' $type/$file > /dev/null");
      if ($? != 0) {
        next;
      }
    }
    if (!($type =~ /(^$)/)) {
      $res = $a->get($url);
      if ($res->is_success()) {
        open FILE, ">:utf8", "$type/$file.tmp";
        print FILE $a->content;
        close FILE;
        system("diff $type/$file $type/$file.tmp 2> /dev/null | grep . > /dev/null");
        if ($? == 0 || !(-e "$type/$file")) {
          rename "$type/$file.tmp", "$type/$file";
          print "$type/$file\n";
        } else {
          unlink("$type/$file.tmp");
        }
      }
      $a->back();
    }
  }
}

