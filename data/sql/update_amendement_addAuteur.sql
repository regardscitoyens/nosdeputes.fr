ALTER TABLE `amendement` ADD `auteur_id` BIGINT NOT NULL AFTER `date` ,
ADD INDEX ( `auteur_id` ) ;
