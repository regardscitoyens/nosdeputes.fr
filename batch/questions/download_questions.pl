#!/usr/bin/perl

use Date::Format;
use WWW::Mechanize;
use HTML::TokeParser;
$count = 0;
$count2 = 0;

open(FILE, 'dernier_numero.txt') ;
@last_record = <FILE>;
$last_record = "@last_record";
close FILE;

$url = "http://recherche2.assemblee-nationale.fr/questions/resultats-questions.jsp?NumLegislature=13Questions&C1=QE&Dates=DPQ&Scope=TEXTEINTEGRAL&SortField=NUM&SortOrder=DESC&format=HTML";
$a = WWW::Mechanize->new();
$a->get($url);
$content = $a->content;
$p = HTML::TokeParser->new(\$content);
while ($t = $p->get_tag('span')) {
    if ($t->[1]{style} eq 'color:#C2262A; font-weight: bold;') {
        $last_number = $p->get_text('/span');
	break;
    }
}

print "Download questions écrites numéro ".($last_record-100)." à ".($last_number+100).'\n\n';

for ($cpt = $last_record-100 ; $cpt < $last_number+100 ; $cpt++) {
    $htmfile = "http://questions.assemblee-nationale.fr/q13/13-".$cpt."QE.htm";
    $htmfile =~ s/^\s+//gi;
    $count++;
    $a->get($htmfile);
    $htmfile =~ s/\//_/gi;
    $htmfile =~ s/\#.*//;
    print "  $htmfile ... ";
    open FILE, ">:utf8", "html/$htmfile";
    $content = $a->content;
    utf8::decode($content);
    print FILE $content;
    close FILE;
    print "downloaded.\n";
    $a->back();
}
print $count." questions récentes\n\n";

open(FILE, 'liste_sans_reponse.txt') ;
@string = <FILE>;
$string = "@string";
close FILE;

foreach $line (split /\n/, $string) {
    $htmfile = $line;
    $htmfile =~ s/^\s+//gi;
    next if ($htmfile =~ /source/);
    $count2++;
    $a->get($line);
    $htmfile =~ s/\//_/gi;
    $htmfile =~ s/\#.*//;
    print "  $htmfile ... ";
    open FILE, ">:utf8", "html/$htmfile";
    $content = $a->content;
    utf8::decode($content);
    print FILE $content;
    close FILE;
    print "downloaded.\n";
    $a->back();
}

print $count2." questions encore sans réponse\n\n";
print $count+$count2." questions téléchargées\n";

