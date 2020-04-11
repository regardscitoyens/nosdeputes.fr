#!/usr/bin/perl

use WWW::Mechanize;
use HTML::TokeParser;

$legislature = shift || 14;
$lastyear = localtime(time);
$lastyear =~ s/^.*\s(\d{4})$/$1/;
$lastyear++;

$a = WWW::Mechanize->new(autocheck => 0);

@urls = ("http://www.assemblee-nationale.fr/$legislature/documents/index-depots.asp",
         "http://www.assemblee-nationale.fr/$legislature/documents/index-rapports.asp",
         "http://www.assemblee-nationale.fr/$legislature/documents/index-application_lois.asp",
         "http://www.assemblee-nationale.fr/$legislature/europe/index-rapinfo.asp",
         "http://www.assemblee-nationale.fr/$legislature/documents/index-information-comper.asp",
         "http://www.assemblee-nationale.fr/$legislature/documents/index-rapports-legislation.asp",
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
  $ct = 0;
  $a->get($baseurl);
  $content = $a->content;
  $p = HTML::TokeParser->new(\$content);
  while ($t = $p->get_tag('a')) {
    $txt = $p->get_text('/a');
    $url = $t->[1]{href};
    if ($url =~ /^\//) {
      $url = "http://www.assemblee-nationale.fr".$url;
    }
    next if $url =~ /(dossiers|i0562.asp)/i;
    next if $url =~ /\.pdf$/i;
    next if !($url =~ /nale\.fr\/$legislature\//);
    next if $url =~ /app\.(eu\.)?readspeaker\.com/i;
    $ct++;
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
      system("grep -e 'pas encore édité' $type/$file > /dev/null");
      if ($? != 0) {
        next;
      }
      system("grep -e 'DOCUMENT_DISTRIBUE\" content=\"N\">' $type/$file > /dev/null");
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
        system("grep -e 'pas encore édité' $type/$file.tmp > /dev/null");
        if ($? != 0 || !(-e "$type/$file")) {
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

