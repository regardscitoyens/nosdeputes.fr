#!/usr/bin/perl

require ("../common/common.pm");
use WWW::Mechanize;
use POSIX qw<mktime strftime>;

$a = WWW::Mechanize->new();

$url = shift;
$deforganisme = shift;
$typeorganisme = shift;
if ($typeorganisme) {
    $typeorganisme = ", \"typeorganisme\": \"$typeorganisme\"";
}

$a->get($url);
$html = $a->content;
utf8::encode($html);
$html =~ s/’/'/g;
$html =~ s/<\/[^ab][^>]*>//gi;
$html =~ s/<[^ba\/][^ba>][^>]*>//gi;
$html =~ s/(\n|<br>)/ /gi;

$html =~ s/<\/b>\s*<b>/ /gi;

$html =~ s/<\/b>/<\/b>\n/g;
$html =~ s/<b>/\n<b>/g;
$html =~ s/\r//g;

sub findDate($) {
	$_ = shift;
	$_ =~ s/&[^;]+;/ /g;

	@mois = ();
	@jours = ();
	$year = '';
	$cpt = 1;

	if (/\D(2\d{3})\D/) {
		$year = $1;
	}
	while(/\D+(\d+)\D+(janvier|f[^v ]*vrier|mars|avril|mai|juin|juillet|ao[^t ]*t|septembre|octobre|novembre|d[^c ]*cembre)/gi) {
		$jours[$cpt] = $1;
		$mois[$cpt--] = $2;
	}
	if ($cpt > -1) {

		$fin = $jours[0] = $jours[1];
		$mois[0] = $mois[1];
		if (/(\d+)\D{1,10}$fin\D/) {
			$jours[0] = $1;
		}
	}

	@datesrange = sort (join('-', datize($jours[0]." ".$mois[0]." ".$year)), join('-', datize($jours[1]." ".$mois[1]." ".$year)));
	@dates =();
	
	my ( $year, $month, $day )  = split /-/, $datesrange[0];
	return () if($year < 2000);
	my ($eyear, $emonth, $eday) = split /-/, $datesrange[1];

	my $end_date   = mktime( 0, 0, 0, $eday, $emonth -1, $eyear - 1900 );
	while ( 1 ) { 
	    my $date = mktime( 0, 0, 0, $day, $month - 1, $year - 1900 );
	    ($sec, $min, $hour, $day, $month, $year, $wday, $yday) = localtime($date);
	    $year += 1900; $month += 1;
	    push @dates, sprintf("%04d-%02d-%02d", $year, $month, $day);
 	    $day++;
	    last if $date >= $end_date;
	}

	return @dates;
}

foreach (split /\n/, $html) {
	if (/^<b>/) {
		$titre = $_;
		$titre =~ s/<[^>]*>//g;
		$titre =~ s/&nbsp;/ /g;
		$titre =~ s/\s+/ /g;
		$titre =~ s/^\s*//;
		$titre =~ s/\s*$//;
		$titre =~ s/\’/\'/g;
		$titre =~ s/&#8217;/'/g;
		$titre =~ s/&#8211;/-/g;
		$titre =~ s/&#8209;/-/g;
		$titre =~ s/\xc2\x92/'/g;
		$titre =~ s/\xc2\x96/-/g;
		@date = findDate($_);
		$organisme = '';
		if($titre =~ /groupe d'amitié/i) {
		    $titre =~ s/\s+\-\s+/-/g;
		    $titre =~ s/- France/-France/gi;
		    $titre =~ s/Groupe d'amitié France \/ /Groupe d'amitié France-/gi;
		    $titre =~ s/Royaume Uni/Royaume-Uni/gi;
		    $titre =~ s/Île Maurice/Île-Maurice/gi;
		    $titre =~ s/Union des Comores/France-Comores/gi;
		    $titre =~ s/Cap Vert/Cap-Vert/gi;
		    $titre =~ s/Burkina Faso/Burkina-Faso/gi;
		    $titre =~ s/France-Union des Comores/France-Comores/gi;
		    $organisme = lc($1) if ($titre =~ /(groupe d'amitié [^:.,\( ]*) ?/i);
		    $organisme =~ s/(\S*)-france/France-$1/i;
		}
		next;
	}
	%id = ();
	while (/fiches_id.(\d+).asp">([^<]*)<\/a>/g) {
		$nom = $2; $id = $1;
		$nom =~ s/députée?s?//;
		next if ($id{$id});
		$id{$id} = 1;
		$nom =~ s/&nbsp;/ /g;
		$organisme = $deforganisme unless($organisme);
		foreach $d (@date) {
			print "{\"depute\":\"$nom\", \"id_an\":\"$id\", \"reunion\":\"$d\", \"commission\":\"$organisme\", \"source\": \"$url\", \"session\":\"$titre\"$typeorganisme}\n";
		}
	}
}

