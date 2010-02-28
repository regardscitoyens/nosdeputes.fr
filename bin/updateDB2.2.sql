
ALTER TABLE `question_ecrite` ADD COLUMN `date_cloture` DATE AFTER `date`;
ALTER TABLE `question_ecrite` ADD COLUMN `motif_retrait` TEXT AFTER `reponse`;
ALTER TABLE `question_ecrite` ADD UNIQUE `uniq_num_idx` (`legislature`, `numero`);
ALTER TABLE `amendement` ADD UNIQUE `uniq_loi_num_idx` (`legislature`, `texteloi_id`, `numero`, `rectif`);
ALTER TABLE `amendement` DROP INDEX `source`;

