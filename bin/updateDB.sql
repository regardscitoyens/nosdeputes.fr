
ALTER TABLE `commentaire_parlementaires` DROP INDEX `unique_idx`;
ALTER TABLE `commentaire_parlementaires` DROP INDEX `parlementaire_id_idx`;
ALTER TABLE `commentaire_parlementaires` DROP FOREIGN KEY `parlementaire_id`;
DELETE FROM `commentaire_parlementaires` WHERE `parlementaire_id` IS NULL;
ALTER TABLE `commentaire_parlementaires` RENAME TO `commentaire_object`;
ALTER TABLE `commentaire_object` CHANGE COLUMN `parlementaire_id` `object_id` BIGINT;
ALTER TABLE `commentaire_object` ADD COLUMN `object_type` VARCHAR(64) AFTER `id`;
UPDATE `commentaire_object` SET `object_type`="Parlementaire";
ALTER TABLE `commentaire_object` ADD UNIQUE `unique_idx` (`object_type`, `object_id`, `commentaire_id`);

ALTER TABLE `seance` ADD COLUMN `nb_commentaires` BIGINT AFTER `id`;
ALTER TABLE `section` ADD COLUMN `nb_commentaires` BIGINT AFTER `id`;
ALTER TABLE `parlementaire` ADD COLUMN `nb_commentaires` BIGINT AFTER `id`;
ALTER TABLE `personnalite` ADD COLUMN `nb_commentaires` BIGINT AFTER `id`;
CREATE TABLE article_loi (id BIGINT AUTO_INCREMENT, nb_commentaires BIGINT, texteloi_id VARCHAR(8), titre VARCHAR(16), ordre BIGINT, precedent VARCHAR(16), suivant VARCHAR(16), expose TEXT, titre_loi_id BIGINT, slug VARCHAR(255), UNIQUE INDEX iloititre_idx (texteloi_id, titre), INDEX iloiarticle_idx (texteloi_id, ordre), INDEX titre_loi_id_idx (titre_loi_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci ENGINE = MyISAM;
CREATE TABLE alinea (id BIGINT AUTO_INCREMENT, nb_commentaires BIGINT, texteloi_id VARCHAR(8), article_loi_id BIGINT, numero BIGINT, texte TEXT, ref_loi VARCHAR(255), created_at DATETIME, updated_at DATETIME, UNIQUE INDEX iarticleloinumero_idx (texteloi_id, article_loi_id, numero), INDEX article_loi_id_idx (article_loi_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci ENGINE = MyISAM;
CREATE TABLE titre_loi (id BIGINT AUTO_INCREMENT, nb_commentaires BIGINT, texteloi_id VARCHAR(8), chapitre BIGINT, section BIGINT, titre TEXT, expose TEXT, parlementaire_id BIGINT, date DATE, source VARCHAR(128) UNIQUE, nb_articles BIGINT, titre_loi_id BIGINT, created_at DATETIME, updated_at DATETIME, INDEX parlementaire_id_idx (parlementaire_id), INDEX titre_loi_id_idx (titre_loi_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci ENGINE = MyISAM;

ALTER TABLE article_loi ADD FOREIGN KEY (titre_loi_id) REFERENCES titre_loi(id);
ALTER TABLE alinea ADD FOREIGN KEY (article_loi_id) REFERENCES article_loi(id);
ALTER TABLE titre_loi ADD FOREIGN KEY (titre_loi_id) REFERENCES titre_loi(id);
ALTER TABLE titre_loi ADD FOREIGN KEY (parlementaire_id) REFERENCES parlementaire(id);

