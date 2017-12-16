<?php

class loadAmdmtsTask extends sfBaseTask {
  protected function configure() {
    $this->namespace = 'load';
    $this->name = 'Amdmts';
    $this->briefDescription = 'Load Amendements data';
    $this->addOption('env', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'prod');
    $this->addOption('app', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'frontend');
    $this->addOption('max', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', '100');
  }

  protected function execute($arguments = array(), $options = array()) {
    // your code here
    $dir = dirname(__FILE__).'/../../batch/amendements/json/';
    $backupdir = dirname(__FILE__).'/../../batch/amendements/loaded/';
    $errordir = dirname(__FILE__).'/../../batch/amendements/errors/';
    $this->configuration = sfProjectConfiguration::getApplicationConfiguration($options['app'], $options['env'], true);
    $manager = new sfDatabaseManager($this->configuration);
    $nb_json = 0;
    $orgas = array(
      "GVT"    => "Gouvernement",
      "59046"  => "Commission de la défense nationale et des forces armées",
      "59047"  => "Commission des affaires étrangères",
      "59048"  => "Commission des finances",
      "59051"  => "Commission des lois",
      "419610" => "Commission des affaires économiques",
      "419865" => "Commission du développement durable et de l'aménagement du territoire",
      "420120" => "Commission des affaires sociales"
    );

    if (is_dir($dir)) {
      if ($dh = opendir($dir)) {
        $ct_lines = 0;
        $ct_crees = 0;
        $ct_modif = 0;
        while (($file = readdir($dh)) != false) {
          if ($file == ".." || $file == ".") continue;
          $nb_json++;
          if ($nb_json > $options['max'])
            break;
          foreach(file($dir.$file) as $line) {
            $ct_lines++;
            $json = json_decode($line);
            if (!$json) {
              echo "ERROR json : $line";
	      rename($dir.$file, $errordir.$file);
              continue 2;
            }
            if (!$json->source || !$json->legislature || !$json->numero || !$json->loi || !$json->sujet || !$json->texte || !$json->date || !isset($json->rectif)) {
              echo "ERROR mandatory arg missing (source|legis|numero|loi|sujet|texte|date|rectif): $line\n";
	      rename($dir.$file, $errordir.$file);
              continue 2;
            }
            $modif = true;
            $new = false;
            $amdmt = Doctrine::getTable('Amendement')->findOneByLegisLoiNumRect($json->legislature, $json->loi, $json->numero, $json->rectif);
            if ($json->rectif > 0) foreach(Doctrine::getTable('Amendement')->findByCleanedSource($json->source) as $rect) {
              if ($rect->rectif < $json->rectif && $rect->texteloi_id == $json->loi && $rect->numero == $json->numero) {
                $rect->sort = "Rectifié";
                $rect->save();
              }
            }

            if ($json->date === "1970-01-01") {
              if ($amdmt)
                $json->date = substr($amdmt->created_at, 0, 10);
              else $json->date = date('Y-m-d');
            }
            if (!$amdmt) {
              $ct_crees++;
              $new = true;
              print "$file -> http://www.nosdeputes.fr/".myTools::getLegislature()."/amendement/".$json->loi."/".$json->numero."  \n";
              $amdmt = new Amendement();
              $amdmt->legislature = $json->legislature;
              $amdmt->texteloi_id = $json->loi;
              $amdmt->addTag('loi:numero='.$amdmt->texteloi_id);
              $amdmt->numero = $json->numero;
              $amdmt->rectif = $json->rectif;
            } elseif (!$json->parent && !$json->serie && $amdmt->signataires == $json->auteurs && ($amdmt->date == $json->date || ($amdmt->texte == $json->texte && $amdmt->expose == $json->expose && $amdmt->sujet == $json->sujet))) {
              $modif = false;
            }
            if ($modif) {
              $ct_modif++;
              $amdmt->source = $json->source;
              $amdmt->date = $json->date;
              $lettre = $amdmt->getLettreLoi();
              if ($json->serie) {
                $nb_serie = 0;
                if (preg_match('/,/', $json->serie)) {
                  $arr = preg_split('/,/', $json->serie);
                  foreach ($arr as $gap_stri) {
                    $gap = preg_split('/-/', $gap_stri);
                    for ($n = $gap[0]; $n <= $gap[1]; $n++) {
                      $amdmt->addTag('loi:amendement='.$n);
                      if ($lettre) {
                        $amdmt->addTag('loi:amendement='.$n.$lettre);
                      }
                      $nb_serie++;
                    }
                  }
                } else {
                  $gap = preg_split('/-/', $json->serie);
                  for ($n = $gap[0]; $n <= $gap[1]; $n++) {
                    $amdmt->addTag('loi:amendement='.$n);
                    if ($lettre) {
                      $amdmt->addTag('loi:amendement='.$n.$lettre);
                    }
                    $nb_serie++;
                  }
                }
                $amdmt->nb_multiples = $nb_serie;
              } else {
                $amdmt->addTag('loi:amendement='.$amdmt->numero);
                if ($lettre) {
                  $num = str_replace($lettre, "", $amdmt->numero);
                  $amdmt->addTag('loi:amendement='.$num);
                }
                $amdmt->nb_multiples = 1;
              }
              if ($json->parent)
                $amdmt->sous_amendement_de = $json->parent.$lettre;
              $amdmt->sujet = $json->sujet;
              $amdmt->texte = $json->texte;
              if ($json->expose)
                $amdmt->expose = $json->expose;
              $amdmt->content_md5 = md5($json->legislature.$json->loi.$json->sujet.$json->texte);
              if ($json->auteurs) {
                $amdmt->signataires = $json->auteurs;
                $amdmt->setAuteurs($json->auteurs);
              } else if (!$json->sort || !preg_match('/(irrecevable|retir)/i', $json->sort)) {
                echo "ERROR json auteurs missing : $line\n";
                $amdmt->free();
	        rename($dir.$file, $errordir.$file);
                continue 2;
              }
            }
            if ($json->sort) {
              $amdmt->sort = $json->sort;
            } elseif (!$amdmt->sort) {
              $amdmt->sort = "Indéfini";
            }
            if ($json->auteur_reel && !isset($orgas[$json->auteur_reel]) && !$amdmt->auteur_id) {
              $parl = Doctrine::getTable('Parlementaire')->findOneByIdAn($json->auteur_reel);
              if ($parl->id)
                $amdmt->setFirstAuteur($parl);
              else print "WARNING: cannot find auteur from ID AN ".$json->auteur_reel." for ".$json->auteurs." in ".$json->source." (Amdmt de commission ?)\n";
            }
            $amdmt->save();
            $amdmt->free();
            if ($new) {
              $reindexWithParls = Doctrine::getTable('Amendement')->findOneByLegisLoiNumRect($json->legislature, $json->loi, $json->numero, $json->rectif);
              $reindexWithParls->save();
            }
          }
          rename($dir.$file, $backupdir.$file);
        }
        if ($ct_modif) echo $ct_lines." amendements lus : ".$ct_modif." écrits dont ".$ct_crees." nouveaux.\n";
        closedir($dh);
      }
    }
  }
}
