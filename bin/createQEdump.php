<?php
$conf = 'db.inc';
$config_file = file($conf, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

foreach ($config_file as $vars) {
  $vars = explode('=', $vars);
  $var[$vars[0]] = trim($vars[1], '"');
}

$PASS = explode('-p', $var['MYSQLID']);
$DBNAME = $var['DBNAME'];
$DBTABLE = "dump_questions_ecrites";
$DBUSER = $var['DBNAME'];
$DBPASS = $PASS[1];
$HOST = "localhost";
$DEST = "/home/nosdeputes/www.regardscitoyens.org/telechargement/donnees/";

try {
  $bdd = new PDO('mysql:host='.$HOST.';dbname='.$DBNAME, $DBUSER, $DBPASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
  $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  $bdd->query('DROP TABLE IF EXISTS `'.$DBTABLE.'`;
  CREATE TABLE `'.$DBTABLE.'` (
  `id` bigint(20) NOT NULL,
  `slug` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `nom` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sexe` varchar(8) COLLATE utf8_unicode_ci DEFAULT NULL,
  `nom_circo` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `num_circo` smallint(2) UNSIGNED DEFAULT NULL,
  `site_web` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `debut_mandat` date DEFAULT NULL,
  `fin_mandat` date DEFAULT NULL,
  `place_hemicycle` smallint(3) UNSIGNED DEFAULT NULL,
  `url_an` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `profession` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `groupe_acronyme` varchar(8) COLLATE utf8_unicode_ci DEFAULT NULL,
  `source` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `legislature` smallint(2) UNSIGNED DEFAULT NULL,
  `numero` bigint(20) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `date_cloture` date DEFAULT NULL,
  `ministere` tinytext COLLATE utf8_unicode_ci,
  `themes` tinytext COLLATE utf8_unicode_ci,
  `question` text COLLATE utf8_unicode_ci,
  `reponse` text COLLATE utf8_unicode_ci,
  `motif_retrait` tinytext COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `slug` (`slug`),
  KEY `nom_circo` (`nom_circo`),
  KEY `num_circo` (`num_circo`),
  KEY `profession` (`profession`),
  KEY `groupe_acronyme` (`groupe_acronyme`),
  UNIQUE KEY `source` (`source`),
  KEY `numero` (`numero`),
  KEY `date` (`date`),
  KEY `date_cloture` (`date_cloture`),
  FULLTEXT KEY `ministere` (`ministere`),
  FULLTEXT KEY `themes` (`themes`),
  FULLTEXT KEY `question` (`question`),
  FULLTEXT KEY `motif_retrait` (`motif_retrait`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;');

$bdd->query('INSERT INTO `'.$DBTABLE.'` SELECT qe.id, pa.slug, pa.nom, pa.sexe, pa.nom_circo, pa.num_circo, pa.site_web, pa.debut_mandat, pa.fin_mandat, pa.place_hemicycle, pa.url_an, pa.profession, pa.groupe_acronyme,  qe.source, qe.legislature, qe.numero, qe.date, qe.date_cloture, qe.ministere, qe.themes, qe.question, qe.reponse, qe.motif_retrait
FROM `question_ecrite` qe
LEFT JOIN `parlementaire` pa
ON qe.parlementaire_id = pa.id;');

exec('mysqldump '.$var['MYSQLID'].' '.$DBNAME.' '.$DBTABLE.' | gzip -v > '.$DEST.$DBTABLE.'.sql.gz');

$bdd->query('DROP TABLE IF EXISTS `'.$DBTABLE.'`;');
}
catch (Exception $error) {
  fprintf(STDERR, 'Error with '.$DBTABLE.'. Msg : '.$error->getMessage()."\"\n");
}
?>