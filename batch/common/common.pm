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
#           print STDERR "pb date : $date\n";
            return ();
        }
        $mois =~ s/\&[^;]+;//g;
        $mois =~ s/[^a-z]\W?//g;
        return ($annee,$mois{$mois},$jour);
}

sub max_date {
  $d1 = shift;
  $d2 = shift;
  return $d2 if (!$d1);
  return $d1 if (!$d2);
  if ($d1 =~ /^(\d+)\/(\d+)\/(\d+)$/) {
    $y = $3; $m = $2; $d = $1;
    if ($d2 =~ /^(\d+)\/(\d+)\/(\d+)$/) {
      return $d1 if ($3 lt $y || ($3 eq $y && $2 lt $m) || ($3 eq $y && $2 eq $m && $1 lt $d));
      return $d2;
    }
  }
  return null;
}

sub trim {
  $t = shift;
  $t =~ s/\s+/ /g;
  $t =~ s/^ //;
  $t =~ s/ $//;
  return $t;
}
