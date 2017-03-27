#!/bin/bash

source ../../bin/db.inc

# Generate csv of metas on réunions for later use in py script
echo "SELECT s.id, s.date, s.moment, s.nb_commentaires, o.nom
      FROM seance s
      LEFT JOIN organisme o ON s.organisme_id = o.id
      WHERE s.type = 'commission'"  |
  mysql $MYSQLID $DBNAME            |
  iconv -f "iso-8859-15" -t "utf-8" > /tmp/seances-metas.tsv

# Identify dates with at least 2 comm réunions with multiples parlementaires interventions
echo "SELECT distinct(seance_id) AS s_id, date, count(id) AS ct
      FROM intervention
      WHERE type = 'commission' AND parlementaire_id IS NOT NULL
      GROUP BY date, seance_id
      ORDER BY date, seance_id" |
  mysql $MYSQLID $DBNAME        |
  awk '{print $2}'              |
  uniq -c                       |
  grep -v " 1 "                 |
  awk '{print $2}'              |
  while read DAT; do
    echo "SELECT id, md5, seance_id, parlementaire_id, intervention
          FROM intervention
          WHERE type = 'commission' AND date = '$DAT'
          ORDER BY seance_id, timestamp"    |
    mysql $MYSQLID $DBNAME                  |
    iconv -f "iso-8859-15" -t "utf-8" > /tmp/interventions-commissions-$DAT.tsv
    python find_duplicate_interventions.py /tmp/interventions-commissions-$DAT.tsv /tmp/seances-metas.tsv && echo
  done

