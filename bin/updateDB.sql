
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

CREATE TABLE article_loi (id BIGINT AUTO_INCREMENT, nb_commentaires BIGINT, texteloi_id BIGINT, numero BIGINT, expose TEXT, titre_loi_id BIGINT, slug VARCHAR(255), INDEX iloiarticle_idx (texteloi_id, numero), UNIQUE INDEX sluggable_idx (slug), INDEX titre_loi_id_idx (titre_loi_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci ENGINE = MyISAM;
CREATE TABLE alinea_version (id BIGINT, nb_commentaires BIGINT, texteloi_id BIGINT, article_loi_id BIGINT, numero BIGINT, texte TEXT, ref_loi VARCHAR(100), ref_art VARCHAR(100), created_at DATETIME, updated_at DATETIME, version BIGINT, PRIMARY KEY(id, version)) DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci ENGINE = MyISAM;
CREATE TABLE alinea (id BIGINT AUTO_INCREMENT, nb_commentaires BIGINT, texteloi_id BIGINT, article_loi_id BIGINT, numero BIGINT, texte TEXT, ref_loi VARCHAR(100), ref_art VARCHAR(100), created_at DATETIME, updated_at DATETIME, version BIGINT, INDEX iarticleloinumero_idx (texteloi_id, article_loi_id, numero), INDEX article_loi_id_idx (article_loi_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci ENGINE = MyISAM;
CREATE TABLE titre_loi (id BIGINT AUTO_INCREMENT, nb_commentaires BIGINT, texteloi_id BIGINT, chapitre BIGINT, section BIGINT, titre TEXT, expose TEXT, parlementaire_id BIGINT, date DATE, source VARCHAR(128) UNIQUE, nb_articles BIGINT, titre_loi_id BIGINT, created_at DATETIME, updated_at DATETIME, INDEX parlementaire_id_idx (parlementaire_id), INDEX titre_loi_id_idx (titre_loi_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci ENGINE = MyISAM;
ALTER TABLE article_loi ADD FOREIGN KEY (titre_loi_id) REFERENCES titre_loi(id);
ALTER TABLE alinea_version ADD FOREIGN KEY (id) REFERENCES alinea(id) ON UPDATE CASCADE ON DELETE CASCADE;
ALTER TABLE alinea ADD FOREIGN KEY (article_loi_id) REFERENCES article_loi(id);
ALTER TABLE titre_loi ADD FOREIGN KEY (titre_loi_id) REFERENCES titre_loi(id);
ALTER TABLE titre_loi ADD FOREIGN KEY (parlementaire_id) REFERENCES parlementaire(id);
