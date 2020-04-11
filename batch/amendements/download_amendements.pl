#!/usr/bin/perl

use Date::Format;
use WWW::Mechanize;
use HTML::TokeParser;
$legislature = shift || 14;
$count = 0;
$count2 = 0;

$day = time2str("%d", time);
$month = time2str("%m", time);
$year = time2str("%Y", time);
$datefin = ($year+1)."-01-01";
if ($day > 7) { $day -= 7; }
else {
    $day += 21;
    if ($month == 1) { $month = 12; $year--; }
    else { $month--; }
}
$datedebut = $year."-".sprintf('%02d', $month)."-".sprintf('%02d', $day);

$url = "http://www2.assemblee-nationale.fr/recherche/query_amendements?typeDocument=amendement&idExamen=&idDossierLegislatif=&numAmend=&idAuteur=&idArticle=&idAlinea=&sort=&dateDebut".$datedebut."=&dateFin=".$datefin."&periodeParlementaire=&texteRecherche=&rows=2500&format=html&tri=datedesc&typeRes=liste&start=";

$a = WWW::Mechanize->new();
$a->get($url);
foreach $line (split /\n/, $a->content) {
    if ($line =~ /^\[/) {
        $content = $line;
        break;
    }
}
foreach $line (split /","/, $content) {
    $line =~ s/\\\//\//g;
    $line =~ s/^.*\|(http:\/\/www.assemblee-nationale.fr\/\d+\/amendements\/[^\|]+)\|.*$/\1/;
    next if (!$line || $line =~ /(index|javascript)/);
    $count++;
    $a->get($line);
    $line =~ s/^\s+//gi;
    $line =~ s/\//_-_/gi;
    $line =~ s/\#.*//;
    print "  $line ... ";
    open FILE, ">:utf8", "html/$line";
    print FILE $a->content;
    close FILE;
    print "downloaded.\n";
    $a->back();
}
print $count." amendements pour les deux dernières semaines\n";

open(FILE, 'liste_sort_indefini.txt') ;
@string = <FILE>;
$string = "@string";
close FILE;

foreach $line (split /\n/, $string) {
    $htmfile = $line;
    $htmfile =~ s/^\s+//gi;
    next if ($htmfile =~ /source/);
    $count2++;
    $a->get($line);
    $htmfile =~ s/\//_-_/gi;
    $htmfile =~ s/\#.*//;
    print "  $htmfile ... ";
    open FILE, ">:utf8", "html/$htmfile";
    print FILE $a->content;
    close FILE;
    print "downloaded.\n";
    $a->back();
}

print $count2." amendements au sort encore indéfini\n";
print $count+$count2." amendements téléchargés\n";

