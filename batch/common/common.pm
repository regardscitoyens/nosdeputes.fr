use HTML::Entities;

%mois = ('janvier'=>'01', 'fvrier'=>'02', 'mars'=>'03', 'avril'=>'04', 'mai'=>'05', 'juin'=>'06', 'juillet'=>'07','aot'=>'08', 'septembre'=>'09', 'octobre'=>'10', 'novembre'=>'11', 'dcembre'=>'12');


sub datize {
        my $date = " ".lc(shift);
        my $theo_annee = shift;
	my $jour, $mois, $annee;
	$date =~ s/&nbsp;/ /g;
        if($date =~ /\D([0-9]{1,2})e?r? +(\S+) +([0-9]+)/) {
	    $jour = sprintf "%02d", $1;
	    $mois = $2; 
	    $annee = $3;
	}elsif ($date =~ /\D([0-9]{1,2})e?r? +(\S+)/) {
	    $jour = sprintf "%02d", $1;
	    $mois = $2; 
	    $annee = $theo_annee;
	}else {
#	    print STDERR "pb date : $date\n";
	    return ();
	}
	$mois =~ s/\&[^;]+;//g;
        $mois =~ s/[^a-z]\W?//g;
        return ($annee,$mois{$mois},$jour);
}

sub quotize {
	my $str = shift;
	$str =~ s/"//g;
	return $str;
}

$heure{'neuf'} = '09';
$heure{'dix'} = '10';
$heure{'onze'} = '11';
$heure{'douze'} = '12';
$heure{'treize'} = '13';
$heure{'quatorze'} = '14';
$heure{'quinze'} = '15';
$heure{'seize'} = '16';
$heure{'dix-sept'} = '17';
$heure{'dix-huit'} = '18';
$heure{'dix-neuf'} = '19';
$heure{'vingt'} = '20';
$heure{'vingt et une'} = '21';
$heure{'vingt-et-une'} = '21';
$heure{'vingt-deux'} = '22';
$heure{'quarante'} = '45';
$heure{'quarante-cinq'} = '45';
$heure{'trente'} = '30';
$heure{'trente-cinq'} = '35';
$heure{'quinze'} = '15';
$heure{'zéro'} = '00';
$heure{'cinq'} = '00';
$heure{''} = '00';

sub heurize {
	my $h = shift;
	$h =~ s/\W+$//;
	$h =~ /(\S+) heures\s*(\S*)/;
	return sprintf("%02d:%02d", $heure{$1}, $heure{$2});
}

$rom{'I'} = 1;
$rom{'II'} = 2;
$rom{'III'} = 3;
$rom{'IV'} = 4;
$rom{'V'} = 5;
$rom{'VI'} = 6;
$rom{'VII'} = 7;
$rom{'VIII'} = 8;
$rom{'IX'} = 9;
$rom{'X'} = 10;
$rom{'XI'} = 11;
$rom{'XII'} = 12;
$rom{'XIII'} = 13;
$rom{'XIV'} = 14;
$rom{'XV'} = 15;
$rom{'XVI'} = 16;
$rom{'XVII'} = 17;
$rom{'XVIII'} = 18;
$rom{'XIX'} = 19;
$rom{'XX'} = 20;
$rom{'XXI'} = 21;
$rom{'XXII'} = 22;
$rom{'XXIII'} = 23;
$rom{'XXIV'} = 24;
$rom{'XXV'} = 25;
$rom{'XXVI'} = 26;
$rom{'XXVII'} = 27;
$rom{'XXVIII'} = 28;
$rom{'XXIX'} = 29;
$rom{'XXX'} = 30;
$rom{'XXXI'} = 31;
$rom{'XXXII'} = 32;
$rom{'XXXIII'} = 33;
$rom{'XXXIV'} = 34;
$rom{'XXXV'} = 35;
$rom{'XXXVI'} = 36;
$rom{'XXXVII'} = 37;
$rom{'XXXVIII'} = 38;
$rom{'XXXIX'} = 39;


sub deromanize {
	my $n = shift;
	return $rom{$n} if ($rom{$n});
	return $n;
}

sub sessionize {
	return ($_[1] <= 9) ? ($_[0]-1).$_[0] :  $_[0].($_[0]+1);
}

sub name_lowerize {
	my $name = shift;
	my $utf = shift;
	utf8::decode($name) if (!$utf);
	$name = decode_entities($name);
	$name =~ s/([A-ZÀÉÈÊËÎÏÔÙÛÜÇ])(\w+ ?)/$1\L$2/g;
	utf8::encode($name) if (!$utf);
	return $name;
}

sub law_numberize {
	my $n = shift;
	my $s = shift;
	$s =~ s/^\(?(\d{4}).*$/$1/;
	return sprintf('%04d%04d-%03d', $s, $s+1, $n);
}

;
