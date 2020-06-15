#!/bin/bash

# echo "OBSOLETE"
# exit

source ./bin/db.inc

php symfony cc
php symfony doctrine:build-model
php symfony doctrine:build-form
php symfony doctrine:build-filters
php symfony doctrine:build-sql

echo "CREATE TABLE parlementaire_scrutin (id BIGINT AUTO_INCREMENT, scrutin_id BIGINT, parlementaire_id BIGINT, parlementaire_groupe_acronyme VARCHAR(16), position VARCHAR(255), position_groupe VARCHAR(255), par_delegation TINYINT(1), delegataire_parlementaire_id BIGINT, mise_au_point_position VARCHAR(255), created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX uniq_index_idx (scrutin_id, parlementaire_id), INDEX index_parlementaire_idx (parlementaire_id), INDEX scrutin_id_idx (scrutin_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = MyISAM" | mysql $MYSQLID $DBNAME
echo "CREATE TABLE scrutin (id BIGINT AUTO_INCREMENT, numero BIGINT, annee BIGINT, numero_semaine BIGINT, date DATE, seance_id BIGINT, nombre_votants BIGINT, nombre_pours BIGINT, nombre_contres BIGINT, nombre_abstentions BIGINT, type VARCHAR(255), sort VARCHAR(255), titre text, texteloi_id BIGINT, amendement_id BIGINT, sujet text, demandeurs text, demandeurs_groupes_acronymes VARCHAR(64), avis_gouvernement VARCHAR(16), avis_rapporteur VARCHAR(16), created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX uniq_index_idx (numero), INDEX seance_id_idx (seance_id), INDEX texteloi_id_idx (texteloi_id), INDEX amendement_id_idx (amendement_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = MyISAM" | mysql $MYSQLID $DBNAME
echo "ALTER TABLE parlementaire_scrutin ADD CONSTRAINT parlementaire_scrutin_scrutin_id_scrutin_id FOREIGN KEY (scrutin_id) REFERENCES scrutin(id)" | mysql $MYSQLID $DBNAME
echo "ALTER TABLE parlementaire_scrutin ADD CONSTRAINT parlementaire_scrutin_parlementaire_id_parlementaire_id FOREIGN KEY (parlementaire_id) REFERENCES parlementaire(id)" | mysql $MYSQLID $DBNAME
echo "ALTER TABLE scrutin ADD CONSTRAINT scrutin_texteloi_id_texteloi_id FOREIGN KEY (texteloi_id) REFERENCES texteloi(id)" | mysql $MYSQLID $DBNAME
echo "ALTER TABLE scrutin ADD CONSTRAINT scrutin_seance_id_seance_id FOREIGN KEY (seance_id) REFERENCES seance(id)" | mysql $MYSQLID $DBNAME
echo "ALTER TABLE scrutin ADD CONSTRAINT scrutin_amendement_id_amendement_id FOREIGN KEY (amendement_id) REFERENCES amendement(id)" | mysql $MYSQLID $DBNAME
