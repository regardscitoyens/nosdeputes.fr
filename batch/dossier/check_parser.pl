#!/usr/bin/perl

$id = shift;

if (!$id) {
	print "USAGE perl parse_dossier.pl <url senat> | perl check_dossier.pl <url senat>\n";
	print "\n";
	print "Vérifie qu'un CSV produit par parse_dossier.pl est bien formé\n";
	print "L'argument passé ne sert qu'à identifier l'url à l'origine du CSV\n";
	exit 1;
}

@data = ('INIT');
$errors = 0;
while(<STDIN>) {
    chomp;
    @csv = split(/;/);
    if (!$data[$csv[4]]) {
	$data[$csv[4]] = $csv[6];
    }elsif ($data[$csv[4]-1] ne 'CMP') {
	print "$id: duplicated entry ".$csv[4]."\n";
	$errors++;
    }
    if ($csv[8] !~ /^http/) {
	print "$id: not valid url ".$csv[8]."\n" ;
	$errors++;
    }elsif($csv[6] =~ /assemblee|senat/ && $csv[8] !~ /$csv[6]/) {
	print "$id: not a chambre url ".$csv[8]."\n";
	$errors++;
    }
}

for ($i = 0 ; $i < $#data ; $i++) {
    unless($data[$i]) {
	print "$id: missing step $i\n" if ($data[$i+1] ne 'CMP');
	$errors++;
    }
}
exit $errors;
