#!/usr/bin/perl

use Date::Format;
use WWW::Mechanize;
use HTML::TokeParser;

$legislature = shift || 14;
$count = 0;
$count2 = 0;

open(FILE, 'dernier_numero.txt') ;
@last_record = <FILE>;
$last_record = "@last_record";
close FILE;

# deprecated
#$url = "http://recherche2.assemblee-nationale.fr/questions/resultats-questions.jsp?NumLegislature=".$legislature."Questions&C1=QE&Dates=DPQ&Scope=TEXTEINTEGRAL&SortField=NUM&SortOrder=DESC&format=HTML";
#$a = WWW::Mechanize->new(autocheck => 0);
#$a->get($url);
#$content = $a->content;
#$p = HTML::TokeParser->new(\$content);
#while ($t = $p->get_tag('span')) {
#    if ($t->[1]{style} eq 'color:#C2262A; font-weight: bold;') {
#        $last_number = $p->get_text('/span');
#        break;
#    }
#}

$url = "http://www2.assemblee-nationale.fr/recherche/resultats_questions";
$a = WWW::Mechanize->new(autocheck => 0);
$a->post($url, ["sort_by" => "numDocument", "sort_order" => "desc", "limit" => "10", "legislature" => $legislature, "ssTypeDocument[]" => "qe"]);
$content = $a->content;
$p = HTML::TokeParser->new(\$content);
while ($t = $p->get_tag('a')) {
    if ($t->[1]{href} =~ /assemblee-nationale\.fr\/q\d+\/\d+-(\d+)QE.html?$/) {
        $last_number = $1;
	    break;
    }
}

$last_record -= 100;
if ($last_record < 0) {
  $last_record = 0;
}
print "Download questions écrites numéro ".$last_record." à ".($last_number+100).'\n\n';

for ($cpt = ($last_record >= 100 ? $last_record-100 : 0) ; $cpt < $last_number+100 ; $cpt++) {
    $htmfile = "http://questions.assemblee-nationale.fr/q".$legislature."/".$legislature."-".$cpt."QE.htm/vue/xml";
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
    $line .= "/vue/xml";
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

