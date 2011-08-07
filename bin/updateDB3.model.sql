
ALTER TABLE `citoyen` ADD `parametres` TEXT NULL AFTER `last_login`;
ALTER TABLE `section` ADD `max_date` DATE NULL AFTER `min_date`;
ALTER TABLE `section` ADD `url_an` VARCHAR(255) NULL AFTER `nb_interventions`;

ALTER TABLE `amendement` CHANGE COLUMN `texteloi_id` `texteloi_id` VARCHAR(12);
ALTER TABLE `alinea` CHANGE COLUMN `texteloi_id` `texteloi_id` VARCHAR(12);
ALTER TABLE `article_loi` CHANGE COLUMN `texteloi_id` `texteloi_id` VARCHAR(12);
ALTER TABLE `titre_loi` CHANGE COLUMN `texteloi_id` `texteloi_id` VARCHAR(12);

CREATE TABLE texteloi (id VARCHAR(12) UNIQUE, nb_commentaires BIGINT, legislature BIGINT, numero BIGINT, annexe VARCHAR(12), type VARCHAR(255), type_details TEXT, categorie VARCHAR(128), url_dossier VARCHAR(255), titre TEXT, date DATE, source VARCHAR(128) UNIQUE, section_id BIGINT, organisme_id BIGINT, signataires TEXT, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX section_id_idx (section_id), INDEX organisme_id_idx (organisme_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = MyISAM;

CREATE TABLE parlementaire_texteloi (id BIGINT AUTO_INCREMENT, parlementaire_id BIGINT, texteloi_id VARCHAR(12), importance BIGINT, fonction VARCHAR(255), created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX parlementaire_id_idx (parlementaire_id), INDEX texteloi_id_idx (texteloi_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = MyISAM;

ALTER TABLE texteloi ADD CONSTRAINT texteloi_organisme_id_organisme_id FOREIGN KEY (organisme_id) REFERENCES organisme(id);
ALTER TABLE texteloi ADD CONSTRAINT texteloi_section_id_section_id FOREIGN KEY (section_id) REFERENCES section(id);

ALTER TABLE alinea ADD INDEX texteloi_id_idx (texteloi_id);
ALTER TABLE amendement ADD INDEX texteloi_id_idx (texteloi_id); 
ALTER TABLE article_loi ADD INDEX texteloi_id_idx (texteloi_id);
ALTER TABLE titre_loi ADD INDEX texteloi_id_idx (texteloi_id);

ALTER TABLE alinea ADD CONSTRAINT alinea_texteloi_id_texteloi_id FOREIGN KEY (texteloi_id) REFERENCES texteloi(id);
ALTER TABLE amendement ADD CONSTRAINT amendement_texteloi_id_texteloi_id FOREIGN KEY (texteloi_id) REFERENCES texteloi(id);
ALTER TABLE article_loi ADD CONSTRAINT article_loi_texteloi_id_texteloi_id FOREIGN KEY (texteloi_id) REFERENCES texteloi(id);
ALTER TABLE titre_loi ADD CONSTRAINT titre_loi_texteloi_id_texteloi_id FOREIGN KEY (texteloi_id) REFERENCES texteloi(id);

CREATE TABLE `parlementaire_photo` (
`id` INT NOT NULL ,
`slug` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
`photo` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
PRIMARY KEY ( `id` )
) ;

