#!/bin/bash

. bin/db.inc

echo "SELECT DISTINCT seance_id, source FROM intervention WHERE intervention LIKE '%sera publi% ult%rieurement%' GROUP BY seance_id" | 
	mysql $MYSQLID $DBNAME | tail -n +2 | while read tupple ; do
		id=$(echo $tupple  | sed 's/ .*//')
		source=$(echo $tupple | sed 's/.* //' | sed 's/#.*//')
		cd batch/commission
		urlfile=$(perl download_one.pl $source)
		if test "$urlfile" && perl parse_commission.pl html/$urlfile > out/$urlfile ; then
			cd - > /dev/null
			if php symfony remove:Seance $id ; then
				echo "$id deleted ($source)"
			fi
		else
			cd - > /dev/null
		fi
done
php symfony load:Commission
