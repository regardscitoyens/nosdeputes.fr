-- phpMyAdmin SQL Dump
-- version 3.3.7deb8
-- http://www.phpmyadmin.net
--
-- Serveur: localhost
-- Généré le : Mar 30 Janvier 2018 à 02:51
-- Version du serveur: 5.1.73
-- Version de PHP: 5.3.3-7+squeeze26

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données: `nosdeputes_prod`
--

-- --------------------------------------------------------

--
-- Structure de la table `alerte`
--

CREATE TABLE IF NOT EXISTS `alerte` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `email` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `query` text COLLATE utf8_unicode_ci,
  `filter` text COLLATE utf8_unicode_ci,
  `query_md5` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `titre` text COLLATE utf8_unicode_ci,
  `confirmed` tinyint(1) DEFAULT NULL,
  `no_human_query` tinyint(1) DEFAULT NULL,
  `period` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `next_mail` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_mail` datetime DEFAULT NULL,
  `citoyen_id` bigint(20) DEFAULT NULL,
  `verif` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `index_uniq_idx` (`email`,`citoyen_id`,`query_md5`),
  KEY `citoyen_id_idx` (`citoyen_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4864 ;

-- --------------------------------------------------------

--
-- Structure de la table `alinea`
--

CREATE TABLE IF NOT EXISTS `alinea` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `nb_commentaires` bigint(20) DEFAULT NULL,
  `texteloi_id` varchar(16) COLLATE utf8_unicode_ci DEFAULT NULL,
  `article_loi_id` bigint(20) DEFAULT NULL,
  `numero` bigint(20) DEFAULT NULL,
  `texte` text COLLATE utf8_unicode_ci,
  `ref_loi` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `iarticleloinumero_idx` (`texteloi_id`,`article_loi_id`,`numero`),
  KEY `article_loi_id_idx` (`article_loi_id`),
  KEY `texteloi_id_idx` (`texteloi_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5122 ;

-- --------------------------------------------------------

--
-- Structure de la table `amendement`
--

CREATE TABLE IF NOT EXISTS `amendement` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `nb_commentaires` bigint(20) DEFAULT NULL,
  `source` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `legislature` bigint(20) DEFAULT NULL,
  `texteloi_id` varchar(16) COLLATE utf8_unicode_ci DEFAULT NULL,
  `numero` varchar(8) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sous_amendement_de` varchar(8) COLLATE utf8_unicode_ci NOT NULL,
  `rectif` bigint(20) DEFAULT NULL,
  `sujet` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sort` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date` date DEFAULT NULL,
  `signataires` text COLLATE utf8_unicode_ci,
  `texte` text COLLATE utf8_unicode_ci,
  `expose` text COLLATE utf8_unicode_ci,
  `content_md5` varchar(36) COLLATE utf8_unicode_ci DEFAULT NULL,
  `nb_multiples` int(11) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_loi_num_idx` (`legislature`,`texteloi_id`,`numero`,`rectif`),
  KEY `texteloi_id_idx` (`texteloi_id`),
  KEY `idx_content_md5` (`content_md5`),
  KEY `idx_numero` (`numero`),
  KEY `idx_sort` (`sort`),
  FULLTEXT KEY `ft_amendements_idx` (`sujet`,`texte`,`expose`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=52472 ;

-- --------------------------------------------------------

--
-- Structure de la table `article`
--

CREATE TABLE IF NOT EXISTS `article` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `nb_commentaires` bigint(20) DEFAULT NULL,
  `titre` varchar(254) COLLATE utf8_unicode_ci DEFAULT NULL,
  `corps` text COLLATE utf8_unicode_ci,
  `user_corps` text COLLATE utf8_unicode_ci,
  `categorie` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `citoyen_id` bigint(20) DEFAULT NULL,
  `article_id` bigint(20) DEFAULT NULL,
  `link` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8_unicode_ci DEFAULT 'brouillon',
  `object_id` bigint(20) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `version` bigint(20) DEFAULT NULL,
  `slug` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `article_sluggable_idx` (`slug`),
  KEY `icategories_idx` (`categorie`),
  KEY `iobject_idx` (`categorie`,`object_id`),
  KEY `ititre_idx` (`categorie`,`titre`(200)),
  KEY `ititrecitoyen_idx` (`categorie`,`titre`(200),`citoyen_id`),
  KEY `iarticle_idx` (`article_id`),
  KEY `citoyen_id_idx` (`citoyen_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=31 ;

-- --------------------------------------------------------

--
-- Structure de la table `article_loi`
--

CREATE TABLE IF NOT EXISTS `article_loi` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `nb_commentaires` bigint(20) DEFAULT NULL,
  `texteloi_id` varchar(16) COLLATE utf8_unicode_ci DEFAULT NULL,
  `titre` varchar(16) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ordre` bigint(20) DEFAULT NULL,
  `precedent` varchar(16) COLLATE utf8_unicode_ci DEFAULT NULL,
  `suivant` varchar(16) COLLATE utf8_unicode_ci DEFAULT NULL,
  `expose` text COLLATE utf8_unicode_ci,
  `titre_loi_id` bigint(20) DEFAULT NULL,
  `slug` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `iloititre_idx` (`texteloi_id`,`titre`),
  KEY `iloiarticle_idx` (`texteloi_id`,`ordre`),
  KEY `titre_loi_id_idx` (`titre_loi_id`),
  KEY `texteloi_id_idx` (`texteloi_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=571 ;

-- --------------------------------------------------------

--
-- Structure de la table `article_version`
--

CREATE TABLE IF NOT EXISTS `article_version` (
  `id` bigint(20) NOT NULL DEFAULT '0',
  `nb_commentaires` bigint(20) DEFAULT NULL,
  `titre` varchar(254) COLLATE utf8_unicode_ci DEFAULT NULL,
  `corps` text COLLATE utf8_unicode_ci,
  `user_corps` text COLLATE utf8_unicode_ci,
  `categorie` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `citoyen_id` bigint(20) DEFAULT NULL,
  `article_id` bigint(20) DEFAULT NULL,
  `link` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8_unicode_ci DEFAULT 'brouillon',
  `object_id` bigint(20) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `version` bigint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`,`version`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `citoyen`
--

CREATE TABLE IF NOT EXISTS `citoyen` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `login` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `password` varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `activite` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `url_site` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `employe_an` tinyint(1) DEFAULT '0',
  `travail_pour` bigint(20) DEFAULT NULL,
  `naissance` date DEFAULT NULL,
  `sexe` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `nom_circo` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `num_circo` bigint(20) DEFAULT NULL,
  `photo` longblob,
  `activation_id` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '0',
  `role` varchar(255) COLLATE utf8_unicode_ci DEFAULT 'membre',
  `last_login` datetime DEFAULT NULL,
  `parametres` text COLLATE utf8_unicode_ci,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `slug` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `login` (`login`),
  UNIQUE KEY `citoyen_sluggable_idx` (`slug`),
  KEY `is_active_idx` (`is_active`),
  KEY `role_idx` (`role`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5109 ;

-- --------------------------------------------------------

--
-- Structure de la table `commentaire`
--

CREATE TABLE IF NOT EXISTS `commentaire` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `rate` bigint(20) DEFAULT NULL,
  `citoyen_id` bigint(20) DEFAULT NULL,
  `commentaire` text COLLATE utf8_unicode_ci,
  `is_public` tinyint(1) DEFAULT NULL,
  `ip_address` text COLLATE utf8_unicode_ci,
  `object_type` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `object_id` varchar(16) COLLATE utf8_unicode_ci DEFAULT NULL,
  `lien` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `presentation` text COLLATE utf8_unicode_ci,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `commentable_idx` (`object_type`,`object_id`),
  KEY `citoyen_id_idx` (`citoyen_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4316 ;

-- --------------------------------------------------------

--
-- Structure de la table `commentaire_object`
--

CREATE TABLE IF NOT EXISTS `commentaire_object` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `object_type` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `object_id` bigint(20) DEFAULT NULL,
  `commentaire_id` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_idx` (`object_type`,`object_id`,`commentaire_id`),
  KEY `commentaire_id_idx` (`commentaire_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=34740 ;

-- --------------------------------------------------------

--
-- Structure de la table `intervention`
--

CREATE TABLE IF NOT EXISTS `intervention` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `nb_commentaires` bigint(20) DEFAULT NULL,
  `nb_mots` bigint(20) DEFAULT NULL,
  `md5` varchar(36) COLLATE utf8_unicode_ci DEFAULT NULL,
  `intervention` text COLLATE utf8_unicode_ci,
  `timestamp` bigint(20) DEFAULT NULL,
  `source` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `seance_id` bigint(20) DEFAULT NULL,
  `section_id` bigint(20) DEFAULT NULL,
  `type` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date` date DEFAULT NULL,
  `personnalite_id` bigint(20) DEFAULT NULL,
  `parlementaire_id` bigint(20) DEFAULT NULL,
  `fonction` text COLLATE utf8_unicode_ci,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `md5` (`md5`),
  KEY `date_intervention_idx` (`date`),
  KEY `section_id_idx` (`section_id`),
  KEY `seance_id_idx` (`seance_id`),
  KEY `personnalite_id_idx` (`personnalite_id`),
  KEY `parlementaire_id_idx` (`parlementaire_id`),
  FULLTEXT KEY `ft_intervention_idx` (`intervention`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=783950 ;

-- --------------------------------------------------------

--
-- Structure de la table `object_commentable`
--

CREATE TABLE IF NOT EXISTS `object_commentable` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `nb_commentaires` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `object_rated`
--

CREATE TABLE IF NOT EXISTS `object_rated` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `rate` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `organisme`
--

CREATE TABLE IF NOT EXISTS `organisme` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `type` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `slug` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nom` (`nom`),
  UNIQUE KEY `organisme_sluggable_idx` (`slug`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=787 ;

-- --------------------------------------------------------

--
-- Structure de la table `parlementaire`
--

CREATE TABLE IF NOT EXISTS `parlementaire` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `nb_commentaires` bigint(20) DEFAULT NULL,
  `nom` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `nom_de_famille` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sexe` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_naissance` date DEFAULT NULL,
  `lieu_naissance` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `nom_circo` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `num_circo` bigint(20) DEFAULT NULL,
  `sites_web` text COLLATE utf8_unicode_ci,
  `debut_mandat` date DEFAULT NULL,
  `fin_mandat` date DEFAULT NULL,
  `place_hemicycle` bigint(20) DEFAULT NULL,
  `url_an` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `profession` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `autoflip` tinyint(1) DEFAULT NULL,
  `id_an` bigint(20) DEFAULT NULL,
  `type` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `groupe_acronyme` varchar(8) COLLATE utf8_unicode_ci DEFAULT NULL,
  `adresses` text COLLATE utf8_unicode_ci,
  `suppleant_de_id` bigint(20) DEFAULT NULL,
  `anciens_mandats` text COLLATE utf8_unicode_ci,
  `autres_mandats` text COLLATE utf8_unicode_ci,
  `anciens_autres_mandats` text COLLATE utf8_unicode_ci,
  `mails` text COLLATE utf8_unicode_ci,
  `top` text COLLATE utf8_unicode_ci,
  `villes` text COLLATE utf8_unicode_ci,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `slug` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `url_ancien_cpc` text COLLATE utf8_unicode_ci NOT NULL,
  `url_nouveau_cpc` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_an` (`id_an`),
  UNIQUE KEY `uniq_url_idx` (`url_an`),
  UNIQUE KEY `parlementaire_sluggable_idx` (`slug`),
  KEY `suppleant_de_id_idx` (`suppleant_de_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=640 ;

-- --------------------------------------------------------

--
-- Structure de la table `parlementaire_amendement`
--

CREATE TABLE IF NOT EXISTS `parlementaire_amendement` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `parlementaire_id` bigint(20) DEFAULT NULL,
  `amendement_id` varchar(36) COLLATE utf8_unicode_ci DEFAULT NULL,
  `numero_signataire` bigint(20) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `parlementaire_id_idx` (`parlementaire_id`),
  KEY `amendement_id_idx` (`amendement_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=496533 ;

-- --------------------------------------------------------

--
-- Structure de la table `parlementaire_organisme`
--

CREATE TABLE IF NOT EXISTS `parlementaire_organisme` (
  `fonction` text COLLATE utf8_unicode_ci,
  `importance` bigint(20) DEFAULT NULL,
  `debut_fonction` date DEFAULT NULL,
  `organisme_id` bigint(20) NOT NULL DEFAULT '0',
  `parlementaire_id` bigint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`organisme_id`,`parlementaire_id`),
  KEY `parlementaire_organisme_parlementaire_id_parlementaire_id` (`parlementaire_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `parlementaire_photo`
--

CREATE TABLE IF NOT EXISTS `parlementaire_photo` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `slug` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `photo` longtext COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=640 ;

-- --------------------------------------------------------

--
-- Structure de la table `parlementaire_texteloi`
--

CREATE TABLE IF NOT EXISTS `parlementaire_texteloi` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `parlementaire_id` bigint(20) DEFAULT NULL,
  `texteloi_id` varchar(16) COLLATE utf8_unicode_ci DEFAULT NULL,
  `importance` bigint(20) DEFAULT NULL,
  `fonction` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `parlementaire_id_idx` (`parlementaire_id`),
  KEY `texteloi_id_idx` (`texteloi_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=66363 ;

-- --------------------------------------------------------

--
-- Structure de la table `personnalite`
--

CREATE TABLE IF NOT EXISTS `personnalite` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `nb_commentaires` bigint(20) DEFAULT NULL,
  `nom` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `nom_de_famille` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sexe` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_naissance` date DEFAULT NULL,
  `lieu_naissance` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `slug` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personnalite_sluggable_idx` (`slug`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3302 ;

-- --------------------------------------------------------

--
-- Structure de la table `presence`
--

CREATE TABLE IF NOT EXISTS `presence` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `parlementaire_id` bigint(20) DEFAULT NULL,
  `seance_id` bigint(20) DEFAULT NULL,
  `nb_preuves` bigint(20) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `parlementaire_id_idx` (`parlementaire_id`),
  KEY `seance_id_idx` (`seance_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=156211 ;

-- --------------------------------------------------------

--
-- Structure de la table `preuve_presence`
--

CREATE TABLE IF NOT EXISTS `preuve_presence` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `presence_id` bigint(20) DEFAULT NULL,
  `type` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `source` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `presence_id_idx` (`presence_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=232045 ;

-- --------------------------------------------------------

--
-- Structure de la table `question_ecrite`
--

CREATE TABLE IF NOT EXISTS `question_ecrite` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `nb_commentaires` bigint(20) DEFAULT NULL,
  `source` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `legislature` bigint(20) DEFAULT NULL,
  `numero` bigint(20) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `date_cloture` date DEFAULT NULL,
  `ministere` text COLLATE utf8_unicode_ci,
  `themes` text COLLATE utf8_unicode_ci,
  `question` mediumtext COLLATE utf8_unicode_ci,
  `reponse` mediumtext COLLATE utf8_unicode_ci,
  `motif_retrait` text COLLATE utf8_unicode_ci,
  `content_md5` varchar(36) COLLATE utf8_unicode_ci DEFAULT NULL,
  `parlementaire_id` bigint(20) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `source` (`source`),
  UNIQUE KEY `uniq_num_idx` (`legislature`,`numero`),
  KEY `parlementaire_id_idx` (`parlementaire_id`),
  KEY `date` (`date`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=132695 ;

-- --------------------------------------------------------

--
-- Structure de la table `rate`
--

CREATE TABLE IF NOT EXISTS `rate` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `object_type` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `object_id` bigint(20) DEFAULT NULL,
  `rate` bigint(20) DEFAULT NULL,
  `citoyen_id` bigint(20) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_idx` (`object_type`,`object_id`,`citoyen_id`),
  KEY `citoyen_id_idx` (`citoyen_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=498 ;

-- --------------------------------------------------------

--
-- Structure de la table `seance`
--

CREATE TABLE IF NOT EXISTS `seance` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `nb_commentaires` bigint(20) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `numero_semaine` bigint(20) DEFAULT NULL,
  `annee` bigint(20) DEFAULT NULL,
  `type` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `moment` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `organisme_id` bigint(20) DEFAULT NULL,
  `tagged` tinyint(1) DEFAULT NULL,
  `session` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_index_idx` (`organisme_id`,`date`,`moment`),
  KEY `index_session_idx` (`session`),
  KEY `index_semaine_idx` (`annee`,`numero_semaine`),
  KEY `index_annee_idx` (`annee`),
  KEY `organisme_id_idx` (`organisme_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=6718 ;

-- --------------------------------------------------------

--
-- Structure de la table `section`
--

CREATE TABLE IF NOT EXISTS `section` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `nb_commentaires` bigint(20) DEFAULT NULL,
  `md5` varchar(36) COLLATE utf8_unicode_ci DEFAULT NULL,
  `titre` text COLLATE utf8_unicode_ci,
  `titre_complet` text COLLATE utf8_unicode_ci,
  `section_id` bigint(20) DEFAULT NULL,
  `min_date` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL,
  `max_date` date DEFAULT NULL,
  `timestamp` bigint(20) DEFAULT NULL,
  `nb_interventions` bigint(20) DEFAULT NULL,
  `id_dossier_an` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `md5` (`md5`),
  KEY `section_id_idx` (`section_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=18194 ;

-- --------------------------------------------------------

--
-- Structure de la table `tag`
--

CREATE TABLE IF NOT EXISTS `tag` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_triple` tinyint(1) DEFAULT NULL,
  `triple_namespace` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `triple_key` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `triple_value` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `name_idx` (`name`),
  KEY `triple1_idx` (`triple_namespace`),
  KEY `triple2_idx` (`triple_key`),
  KEY `triple3_idx` (`triple_value`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=20345 ;

-- --------------------------------------------------------

--
-- Structure de la table `tagging`
--

CREATE TABLE IF NOT EXISTS `tagging` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `tag_id` bigint(20) NOT NULL,
  `taggable_model` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `taggable_id` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tag_idx` (`tag_id`),
  KEY `taggable_idx` (`taggable_model`,`taggable_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2059336 ;

-- --------------------------------------------------------

--
-- Structure de la table `texteloi`
--

CREATE TABLE IF NOT EXISTS `texteloi` (
  `id` varchar(16) COLLATE utf8_unicode_ci DEFAULT NULL,
  `nb_commentaires` bigint(20) DEFAULT NULL,
  `legislature` bigint(20) DEFAULT NULL,
  `numero` bigint(20) DEFAULT NULL,
  `annexe` varchar(12) COLLATE utf8_unicode_ci DEFAULT NULL,
  `type` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `type_details` text COLLATE utf8_unicode_ci,
  `categorie` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `id_dossier_an` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `titre` text COLLATE utf8_unicode_ci,
  `date` date DEFAULT NULL,
  `source` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `organisme_id` bigint(20) DEFAULT NULL,
  `signataires` text COLLATE utf8_unicode_ci,
  `contenu` text COLLATE utf8_unicode_ci,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  UNIQUE KEY `id` (`id`),
  UNIQUE KEY `source` (`source`),
  UNIQUE KEY `index_alpha_idx` (`numero`,`annexe`),
  KEY `index_url_idx` (`id_dossier_an`),
  KEY `index_type_idx` (`type`(30),`type_details`(200)),
  KEY `index_date_idx` (`date`),
  KEY `organisme_id_idx` (`organisme_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `titre_loi`
--

CREATE TABLE IF NOT EXISTS `titre_loi` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `nb_commentaires` bigint(20) DEFAULT NULL,
  `texteloi_id` varchar(16) COLLATE utf8_unicode_ci DEFAULT NULL,
  `chapitre` varchar(8) COLLATE utf8_unicode_ci DEFAULT NULL,
  `section` varchar(8) COLLATE utf8_unicode_ci DEFAULT NULL,
  `titre` text COLLATE utf8_unicode_ci,
  `expose` text COLLATE utf8_unicode_ci,
  `parlementaire_id` bigint(20) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `source` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `nb_articles` bigint(20) DEFAULT NULL,
  `titre_loi_id` bigint(20) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `source` (`source`),
  KEY `parlementaire_id_idx` (`parlementaire_id`),
  KEY `titre_loi_id_idx` (`titre_loi_id`),
  KEY `texteloi_id_idx` (`texteloi_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=95 ;

-- --------------------------------------------------------

--
-- Structure de la table `variable_globale`
--

CREATE TABLE IF NOT EXISTS `variable_globale` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `champ` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `value` longblob,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=78 ;
