for file in $(perl download_via_recherche.pl); do
	perl parse_hemicycle.pl html/$file > out/$file ;
	echo out/$file done;
done
