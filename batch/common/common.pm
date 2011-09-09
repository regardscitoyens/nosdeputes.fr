use HTML::Entities;

%mois = ('janvier'=>'01', 'fvrier'=>'02', 'mars'=>'03', 'avril'=>'04', 'mai'=>'05', 'juin'=>'06', 'juillet'=>'07','aot'=>'08', 'septembre'=>'09', 'octobre'=>'10', 'novembre'=>'11', 'dcembre'=>'12');


sub datize {
        my $date = shift;
        $date =~ /([0-9]+)e?r? (\S*) ([0-9]+)/;
        my $jour = sprintf "%02d", $1;
        my $mois = $2;
        my $annee = $3;
	$mois =~ s/&[^;]+;//g;
        $mois =~ s/[^a-z]\W?//g;
        return ($annee,$mois{$mois},$jour);
}

sub quotize {
	my $str = shift;
	$str =~ s/"/&quot;/g;
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
	$h =~ /(\S+) heures\s*(\S*)/;
	return sprintf("%02d:%02d", $heure{$1}, $heure{$2});
}

sub sessionize {
	return ($_[1] <= 8) ? ($_[0]-1).$_[0] :  $_[0].($_[0]+1);
}

sub name_lowerize {
	my $name = shift;
	utf8::decode($name);
	$name = decode_entities($name);
	$name =~ s/([A-ZÀÉÈÊËÎÏÔÙÛÜÇ])(\w+ ?)/$1\L$2/g;
	utf8::encode($name);
	return $name;
}

sub law_numberize {
	my $n = shift;
	my $s = shift;
	$s =~ s/^(\d{4}).*$/$1/;
	return sprintf('%04d%04d-%03d', $s, $s+1, $n);
}

;
