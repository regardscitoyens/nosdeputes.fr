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
    $this->configuration = sfProjectConfiguration::getApplicationConfiguration($options['app'], $options['env'], true);
    $manager = new sfDatabaseManager($this->configuration);
    $nb_json = 0;

    if (is_dir($dir)) {
      if ($dh = opendir($dir)) {
        while (($file = readdir($dh)) != false) {
          if ($file == ".." || $file == ".") continue;
          $ct_lines = 0;
          $ct_lus = 0;
          $ct_crees = 0;
          foreach(file($dir.$file) as $line) {
            $ct_lines++;
            $nb_json++;
            if ($nb_json > $options['max'])
            break 2;
            $json = json_decode($line);
            if (!$json) {
              echo "ERROR json : $line";
              continue;
            }
            if (!$json->source || !$json->legislature || !$json->numero || !$json->loi || !$json->sujet || !$json->texte || !$json->date || !isset($json->rectif)) {
              echo "ERROR mandatory arg missing (source|legis|numero|loi|sujet|texte|date|rectif): $line\n";
              continue;
            }
            $ct_lus++;
            $modif = true;
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
              print "$file -> http://www.nosdeputes.fr/14/amendement/".$json->loi."/".$json->numero."  \n";
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
                continue;
              }
            }
            if ($json->sort) {
              $amdmt->sort = $json->sort;
            } elseif (!$amdmt->sort) {
              $amdmt->sort = "Indéfini";
            }
            if ($json->auteur_reel) {
              $amdmt->setAuteur(Doctrine::getTable('Parlementaire')->findOneByIdAn($json->auteur_reel));
            }
            $amdmt->save();
            $amdmt->free();
          }
          unlink($dir.$file);
        }
        if ($ct_crees) echo $ct_lines." amendements lus : ".$ct_lus." écrits dont ".$ct_crees." nouveaux.\n";
        closedir($dh);
      }
    }
  }
}
