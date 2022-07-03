#!/usr/bin/perl

use WWW::Mechanize;
use HTML::TokeParser;

$legislature = shift || 15;

$a = WWW::Mechanize->new(autocheck => 0);
$b = WWW::Mechanize->new(autocheck => 0);
$page = 0;
while ($page < 20) {
  $offset = 10 * $page;
  $url = "https://www2.assemblee-nationale.fr/recherche/resultats_recherche/(offset)/$offset/(tri)/date/(legislature)/$legislature/(query)/eyJxIjoidHlwZURvY3VtZW50OlwiY29tcHRlIHJlbmR1XCIgYW5kIGNvbnRlbnU6YSIsInJvd3MiOjEwLCJzdGFydCI6MCwid3QiOiJwaHAiLCJobCI6ImZhbHNlIiwiZmwiOiJ1cmwsdGl0cmUsdXJsRG9zc2llckxlZ2lzbGF0aWYsdGl0cmVEb3NzaWVyTGVnaXNsYXRpZix0ZXh0ZVF1ZXN0aW9uLHR5cGVEb2N1bWVudCxzc1R5cGVEb2N1bWVudCxydWJyaXF1ZSx0ZXRlQW5hbHlzZSxtb3RzQ2xlcyxhdXRldXIsZGF0ZURlcG90LHNpZ25hdGFpcmVzQW1lbmRlbWVudCxkZXNpZ25hdGlvbkFydGljbGUsc29tbWFpcmUsc29ydCIsInNvcnQiOiIifQ==";
  $page++;

  $a->post($url);
  $content = $a->content;
  $p = HTML::TokeParser->new(\$content);
  while ($t = $p->get_tag('a')) {
    $txt = $p->get_text('/a');
    $source_url = $t->[1]{href};
    $source_url =~ s/(^[\s\t]+|[\s\t]+$)//g;
    $file = $source_url;
    if ($txt =~ /compte rendu|e nationale \~/i && $source_url !~ /(nale\.fr\/dyn\/c\d+\.asp|\/cri\/(2|congres)|\(typeDoc\))/ && $source_url =~ /nationale\.fr\/$legislature\//) {

      $file =~ s/http:/https:/;
      $file =~ s/\//_/gi;
      $file =~ s/\#.*//;
      next if -e "html/$file";

      $b->get($source_url);
      $text = $b->content;

      if ($text !~ /href="(\/dyn\/opendata\/[^"]+\.html)"/) {
        print STDERR "WARNING: opendata raw html url not found for $source_url\n";
        next;
      }
      $raw_url = "https://www.assemblee-nationale.fr$1";
      $opendata_id = $raw_url;
      $opendata_id =~ s/^.*opendata\///;
      next if -e "raw/$opendata_id";

      open FILE, ">:utf8", "html/$file.tmp";
      print FILE $text;
      close FILE;
      rename "html/$file.tmp", "html/$file";

      $b->get($raw_url);
      open FILE, ">:utf8", "raw/$opendata_id.tmp";
      print FILE $b->content;
      close FILE;
      rename "raw/$opendata_id.tmp", "raw/$opendata_id";
      open FILE, ">:utf8", "raw/$opendata_id.url";
      print FILE $raw_url;
      close FILE;

      print "raw/$opendata_id html/$file $source_url\n";
    }
  }
}

