
#!/usr/bin/perl

open FILE, shift;
$source = shift;


$mois{'janvier'} = '01';
$mois{'février'} = '02';
$mois{'mars'} = '03';
$mois{'avril'} = '04';
$mois{'mai'} = '05';
$mois{'juin'} = '06';
$mois{'juillet'} = '07';
$mois{'août'} = '08';
$mois{'septembre'} = '09';
$mois{'octobre'} = '10';
$mois{'novembre'} = '11';
$mois{'décembre'} = '12';

$on = 0;
while(<FILE>) {
    s/&nbsp;/ /g;
    if (!$on && /<b>C?O?M?MISSION /i) {
	$_ = "$_\n";
        $on = 1;
    }
    if (/<b>Convocation|<b>Réunion|<b>Composition/i) {
	$on = 0;
    }
    if ($on) {
	chomp;
	s/<br>/ /g;
	$lines .= $_;
    }
    if (/Membres? présents? ou excusés?/) {
	$on = 1;
    }
}
$lines =~ s/\<hr\>\<a[^>]+\><\/a>[^<]*JOURNAL OFFICIEL DE LA RÉPUBLIQUE FRANÇAISE Texte \d+ sur \d+//gi;
$lines =~ s/\.\<br\>//gi;
$lines =~ s/ +\. +/ /g;
$lines =~ s/ +\. +/ /g;

$lines =~ s/\.<br>\n/<br>\n<br>/g;
$lines =~ s/Membres? présents? ou excusés?//;
$lines =~ s/<\/b>,?à/<\/b>\nà/g;
$lines =~ s/&nbsp;:(<br>)?/ :\n/g;
$lines =~ s/&nbsp;<b>/ /g;
$lines =~ s/&nbsp;/ /g;
$lines =~ s/([^\.>]) \n/$1 /g;
$lines =~ s/<\/b> *<b>/ /g;
$lines =~ s/<\/b>/<\/b>\n/g;
$lines =~ s/<\/i>/<\/i>\n/g;
$lines =~ s/\. <b>/<b>\n/g;
$lines =~ s/<b>/\n<b>/g;
$lines =~ s/<\/?b>//g;
$lines =~ s/\. \. \. <hr>/\n<hr>/;


$lines =~ s/\. <A href[^\n]*//ig;
$lines =~ s/du\s*(<b>|\n)/du /g;
$lines =~ s/\nà\s*.<i>/à /g;
$lines =~ s/, à (<i>)?/ à /g;
$lines =~ s/ : Pr/ <i>Pr/g;
$lines =~ s/<i>/\n<i>/g;
$lines =~ s/\.\s*\n/\n/g;


$lines =~ s/,? ?M(\.|mes?) /\n/g;
$lines =~ s/<\/?\w>\n/\n/g;
$lines =~ s/ : ?\n/\n/g;
$lines =~ s/– //g;
$lines =~ s/\<hr.*Texte \d+ sur \d+\s*//g;

$lines =~ s/\<A .*\<\/a\>\s*//ig;
$lines =~ s/,? ?\<hr/\n<hr/;
$lines =~ s/<[^ib][^>]+>//g;
$lines =~ s/\n([^\s<]+)\s\n+(\S+)\n/\n$1 $2\n/g;
$lines =~ s/(\d[erm]+ r|R)éunion /\n$1éunion /gi;
$lines =~ s/(\.|\;) /$1\n/g;

foreach (split /\n/, $lines) {
#    print "l: $lines\n";
    if (/(Comité\W|Commission\W|Mission\W|Office|Observatoire|Délégation)/i && !/Ordre du jour/ && !/(réunion|séance|nommé)/i && !/Membres/i && !/^\s*\(/ && length($_) < 250) {
	$commissiontmp = $_;
	$commissiontmp =~ s/.*\W(Comité|Commission|Mission|Office|Observatoire|Délégation)/$1/i;
	$commissiontmp =~ s/\s*[\(:].*//;
	$commissiontmp =~ s/[, \)]+$//;
	$commissiontmp =~ s/\W+$//;
	$commission = $commissiontmp if ($commissiontmp =~ /^[A-Z]/);
	$on = 0;
    }
    if (/(réunion|séance)/i) {
	s/ heures/:00/;
	s/ h /:/;
	if (/([\d]+)[er]* ([\wéû]+) (\d+)/) {
	    $date = "$3-".$mois{$2}.'-'.sprintf("%02d",$1);
	}
	if (/ à ([\d:]+)/) {
	    $heure = $1;
	}elsif (/(\d+\S+ (réunion|séance))/) {
	    $heure = $1;
	}
	$on = 0;
    }
    if (/(<i>Excus|<i>Ont d|Ordre|Convocation|Excusés|<b>Nomination|^Nomination)/) {
	$on = 0;
    }
    if ($on && /\w/) {
	foreach $d (split /\, / ) { #/
	    chomp($d);
	    if ($d =~ s/ (\S)\.//) {
		$d = $1.' '.$d;
	    }
	    $d =~ s/\([^\)]+\)//;
	    $d =~ s/^\W+//;
	    $d =~ s/[^àâéèêëîïôùûü\w]+$//;
	    $d =~ s/ \(.*//;

	    if ($d =~ s/(.*)(,| et | ; | \d+| +\. ?)(.*)/$1/) { 
		$nextd = $3;
	    }
	    $d =~ s/( et|[^àâéèêëîïôùûü\w]+)$//;
	    $d =~ s/ ?- ?/-/;
	    $d =~ s/  */ /;

	    next if (length($d) < 3);
	    next if ($d =~ /^.\>/);
	    next unless ($d =~ /[A-Z]/);
	    next if ($d =~ /[A-Z]{2}/);
	    next unless ($d =~ /^[A-Z]/);

	    print '{ ';
	    print '"date": "'.$date.'",';
	    print '"heure": "'.$heure.'",';
	    print '"commission": "'.$commission.'",';
	    print '"senateur": "'.$d.'",';
	    print '"source": "Journal officiel du '.$source.'"';
	    print " } \n";
	    if ($nextd) {
		$d = $nextd;
		$d =~ s/ (et|\.)$//;
		$d =~ s/ ?- ?/-/;
		$d =~ s/  */ /;
		next unless ($d =~ /[A-Z]/);
		next if ($d =~ /[A-Z]{2}/);
		next unless ($d =~ /^[A-Z]/);
		print '{ ';
		print '"date": "'.$date.'",';
		print '"heure": "'.$heure.'",';
		print '"commission": "'.$commission.'",';
		print '"senateur": "'.$d.'",';
		print '"source": "Journal officiel du '.$source.'"';
		print " } \n";
	    }
	}
    }
    if (/<i>(Présents?\W|Assistai)/) {
	$on = 1;
    }
}
