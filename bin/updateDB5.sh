. bin/db.inc

echo 'ALTER TABLE amendement ADD sous_amendement_de VARCHAR(8) NOT NULL AFTER numero' | mysql $MYSQLID $DBNAME
echo 'select a.id, a.texteloi_id, a.numero, t.triple_value from amendement a, tagging tg, tag t WHERE a.sort <> "Rectifié" and a.id = tg.taggable_id AND t.id = tg.tag_id AND t.triple_key = "sous_amendement_de"' |  mysql $MYSQLID $DBNAME | grep ^[0-9] | sed 's/\([A-F]\)\t/;\1;/'| sed 's/\t/;;/g' | awk -F';' '{print $1";"$3";"$5$6";"$7$6}'|awk -F';' '{print "update amendement set sous_amendement_de = \""$4"\" where id = "$1";"}' | mysql $MYSQLID $DBNAME

echo 'ALTER TABLE amendement ADD nb_multiples INT NOT NULL DEFAULT 1 AFTER content_md5 ' | mysql $MYSQLID $DBNAME
echo 'select a.id, count(tg.id) FROM tagging tg LEFT JOIN amendement a ON a.id = tg.taggable_id LEFT JOIN tag t ON t.id = tg.tag_id  WHERE a.sort <> "Rectifié" AND t.triple_key = "amendement" AND taggable_model = "Amendement" group by a.id' |  mysql $MYSQLID $DBNAME | grep ^[0-9]  | awk '{print "UPDATE amendement SET nb_multiples = "$2" WHERE id = "$1";"}'  | mysql $MYSQLID $DBNAME

php symfony doctrine:build-model