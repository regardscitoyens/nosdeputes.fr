#!/usr/bin/perl

open FILE, shift;

$on = 0;
while(<FILE>) {
    if ($on) {
	chomp;
	s/<br>/ /g;
	$lines .= $_;
    }
    if (/Membres présents ou excusés/) {
	$on = 1;
    }
}
$lines =~ s/<\/b> *<b>/ /g;
$lines =~ s/<\/b>/<\/b>\n/g;
$lines =~ s/<\/i>/<\/i>\n/g;
$lines =~ s/\. <b>/<b>\n/g;
$lines =~ s/<b>/\n<b>/g;
$lines =~ s/<\/b>//g;
$lines =~ s/\. \. \. <hr>[^\n]*//;
$lines =~ s/\. <A href[^\n]*//g;
$lines =~ s/ Réunion/\nRéunion/g;
$lines =~ s/du\s*<b>/du /g;
$lines =~ s/\nà\s*.<i>/à /g;
$lines =~ s/, à (<i>)?/ à /g;
$lines =~ s/ : Pr/ <i>Pr/g;
$lines =~ s/<i>/\n<i>/g;
$lines =~ s/\.\s*\n/\n/g;
$lines =~ s/,? ?M(\.|me) /\n/g;
$lines =~ s/<\/?\w>\n/\n/g;
$lines =~ s/ : ?\n/\n/g;
$lines =~ s/– //g;
$lines =~ s/\. /\n/g;

#print $lines; exit;

foreach (split /\n/, $lines) {
    if (/commission|mission/i) {
	$commission = $_;
	$on = 0;
    }
    if (/(réunion|séance)/i) {
	s/ heures/:00/;
	s/ h /:/;
	if (/([\d]+)[er]* ([\wé]+) (\d+)/) {
	    $reunion = "$1 $2 $3";
	}
	if (/ à ([\d:]+)/) {
	    $session = $1;
	}elsif (/(\d+\S+ (réunion|séance))/) {
	    $session = $1;
	}
	$on = 0;
    }
    if (/<i>Excus/) {
	$on = 0;
    }
    if ($on && /\w/) {
	foreach $d (split /\, / ) {
	print "<presence>";
	print "<r>$reunion</r>";
	print "<s>$session</s>";
	print "<c>$commission</c>";
	print "<d>$d</d>";
	print "</presence>\n";
}
    }
    if (/<i>(Présents|Assistait)/) {
	$on = 1;
    }
}
