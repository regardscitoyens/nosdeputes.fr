#!/bin/bash

echo "OBSOLETE"
exit

source ./bin/db.inc

php symfony cc
php symfony doctrine:build-model
php symfony doctrine:build-form
php symfony doctrine:build-filters
php symfony doctrine:build-sql

echo "DROP TABLE titre_loi" | mysql $MYSQLID $DBNAME
echo "CREATE TABLE titre_loi (id BIGINT AUTO_INCREMENT, nb_commentaires BIGINT, texteloi_id VARCHAR(16), leveltype VARCHAR(16), level1 VARCHAR(8), level2 VARCHAR(8), level3 VARCHAR(8), level4 VARCHAR(8), titre TEXT, expose TEXT, parlementaire_id BIGINT, date DATE, source VARCHAR(128) UNIQUE, nb_articles BIGINT, titre_loi_id BIGINT, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX parlementaire_id_idx (parlementaire_id), INDEX titre_loi_id_idx (titre_loi_id), INDEX texteloi_id_idx (texteloi_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = MyISAM"  | mysql $MYSQLID $DBNAME
echo "ALTER TABLE article_loi ADD CONSTRAINT article_loi_titre_loi_id_titre_loi_id FOREIGN KEY (titre_loi_id) REFERENCES titre_loi(id)" | mysql $MYSQLID $DBNAME
echo "ALTER TABLE titre_loi ADD CONSTRAINT titre_loi_titre_loi_id_titre_loi_id FOREIGN KEY (titre_loi_id) REFERENCES titre_loi(id)" | mysql $MYSQLID $DBNAME
echo "ALTER TABLE titre_loi ADD CONSTRAINT titre_loi_texteloi_id_texteloi_id FOREIGN KEY (texteloi_id) REFERENCES texteloi(id)" | mysql $MYSQLID $DBNAME
echo "ALTER TABLE titre_loi ADD CONSTRAINT titre_loi_parlementaire_id_parlementaire_id FOREIGN KEY (parlementaire_id) REFERENCES parlementaire(id)" | mysql $MYSQLID $DBNAME

