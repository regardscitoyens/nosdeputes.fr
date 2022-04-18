#!/usr/bin/perl

use utf8;
use WWW::Mechanize;
use HTML::TokeParser;

$legislature = shift || 15;

$searchurl = "https://www2.assemblee-nationale.fr/recherche/resultats_recherche/(tri)/date/(legislature)/$legislature/(query)/eyJxIjoidHlwZURvY3VtZW50OlwiY29tcHRlIHJlbmR1XCIgYW5kIGNvbnRlbnU6ZGUiLCJyb3dzIjoxMCwic3RhcnQiOjAsInd0IjoicGhwIiwiaGwiOiJmYWxzZSIsImZsIjoic2NvcmUsdXJsLHRpdHJlLHVybERvc3NpZXJMZWdpc2xhdGlmLHRpdHJlRG9zc2llckxlZ2lzbGF0aWYsdGV4dGVRdWVzdGlvbix0eXBlRG9jdW1lbnQsc3NUeXBlRG9jdW1lbnQscnVicmlxdWUsdGV0ZUFuYWx5c2UsbW90c0NsZXMsYXV0ZXVyLGRhdGVEZXBvdCxzaWduYXRhaXJlc0FtZW5kZW1lbnQsZGVzaWduYXRpb25BcnRpY2xlLHNvbW1haXJlLHNvcnQifQ==";

$a = WWW::Mechanize->new();

$done = 0;
while ($searchurl && $done < 50) {
    #print "$searchurl\n";
    $a->get($searchurl);
    $content = $a->content;
    $p = HTML::TokeParser->new(\$content);
    $searchurl = "";
    while ($t = $p->get_tag('a')) {
        $txt = $p->get_text('/a');
        $txt =~ s/[\t\r\n\s]+/ /g;
        $txt =~ s/^\s+|\s+$//g;
        if ($txt =~ /compte rendu intégral|séance (unique )?du /i) {
            $link = $t->[1]{href};
            $link =~ s/[\t\r\n\s]+/ /g;
            $link =~ s/^\s+|\s+$//g;
            $link =~ s/\/dyn\/comptes-rendus\/seance\/redirect\?url=//;
            $file = $link;
            $file =~ s/^https:/http:/;
            $file =~ s/\//_/g;
            $file =~ s/\#.*//;
            if (-e "html/$file") {
              $done++;
            } else {
              print "$link\n";
            }
        } elsif ($txt =~ /^Suivant/) {
            $link = $t->[1]{href};
            $searchurl = "https://www2.assemblee-nationale.fr$link";
        }
    }
}
