<?php

class updateAmdmtsTask extends sfBaseTask {
  protected function configure() {
    $this->namespace = 'update';
    $this->name = 'Amdmts';
    $this->briefDescription = 'Update Amendements data to set auteur_id';
    $this->addOption('env', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'test');
    $this->addOption('app', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'frontend');
    $this->addOption('max', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', '10');
  }

  protected function execute($arguments = array(), $options = array()) {
    // your code here
    $dir = dirname(__FILE__).'/../../batch/amendements/OpenDataAN/';
    $this->configuration = sfProjectConfiguration::getApplicationConfiguration($options['app'], $options['env'], true);
    $manager = new sfDatabaseManager($this->configuration);
    $nb_json_files = 0;

    if (is_dir($dir)) {
      if ($dh = opendir($dir)) {
        while (($file = readdir($dh)) != false) {
          print "$file\n";
          if (substr($file, 0, 6) != 'amdts_') continue;
          if ($nb_json_files > $options['max'])
            break;
          $nb_json_files++;
          $ct_lines = 0;
          $ct_lus = 0;
          $ct_crees = 0;
          foreach(file($dir.$file) as $line) {
            $ct_lines++;
            $json = json_decode($line);
            if (!$json) {
              echo "ERROR json : $line";
              continue;
            }
            if (!$json->legislature || !$json->numero || !$json->loi || !$json->sujet || !isset($json->rectif)) {
              echo "ERROR mandatory arg missing (source|legis|numero|loi|sujet|texte|date|rectif): $line\n";
              continue;
            }
            $amdmt = Doctrine::getTable('Amendement')->findLastOneByLegisLoiNum($json->legislature, $json->loi, $json->numero);
            if (!$amdmt) {
              echo "ERROR amdmt from OpenData AN missing from ND data: $line\n";
              # TODO load missing ones
              continue;
            }
            if ($json->auteur_reel) {
              $parl = Doctrine::getTable('Parlementaire')->findOneByIdAn($json->auteur_reel);
              if (!$parl) {
                echo "ERROR, cannot find auteur from AN ID: $line\n";
                continue;
              }
              $ct_lus++;
              $amdmt->setAuteur($parl);
              $amdmt->save();
            }
            $amdmt->free();
          }
          unlink($dir.$file);
        }
        if ($ct_lines) echo $ct_lines." amendements lus : ".$ct_lus." mis à jour dont ".$ct_crees." nouveaux.\n";
        closedir($dh);
      }
    }
  }
}
