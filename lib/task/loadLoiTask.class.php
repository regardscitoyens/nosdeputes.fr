<?php

class loadLoiTask extends sfBaseTask {
  protected function configure() {
    $this->namespace = 'load';
    $this->name = 'Loi';
    $this->briefDescription = 'Load Loi';
    $this->addOption('env', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'test');
    $this->addOption('app', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'frontend');
  }

  protected function execute($arguments = array(), $options = array()) {
  // your code here
    $dir = dirname(__FILE__).'/../../batch/loi/json/';
    $manager = new sfDatabaseManager($this->configuration);

    if (is_dir($dir)) {
      if ($dh = opendir($dir)) {
        while (($file = readdir($dh)) != false) {
          if ($file == ".." || $file == ".") continue;
          foreach(file($dir.$file) as $line) {
            $json = json_decode($line);
            if (!$json || !$json->type) {
              print $json->type;
              echo "ERROR json : $line\n";
              continue;
            }
            if ($json->type == 'loi') {
              $loi = Doctrine::getTable('TitreLoi')->findLoiOrCreate($json->loi);
              if ($json->source) $loi->source = $json->source;
              if ($json->titre) $loi->titre = $json->titre;
              if ($json->expose) $loi->expose = $json->expose;
              if ($json->date) $loi->date = $json->date;
              if ($json->auteur) $loi->setAuteur($json->auteur);
              $loi->save();
            } else if ($json->type == 'chapitre') {
              $chap = Doctrine::getTable('TitreLoi')->findChapitreOrCreate($json->loi, $json->chapitre);
              if ($json->titre) $chap->titre = $json->titre;
              if ($json->expose) $chap->expose = $json->expose;
              $chap->save();
            } else if ($json->type == 'section') {
              $sec = Doctrine::getTable('TitreLoi')->findSectionOrCreate($json->loi, $json->chapitre, $json->section);
              if ($json->titre) $sec->titre = $json->titre;
              if ($json->expose) $sec->expose = $json->expose;
              $sec->save();
            } else if ($json->type == 'article') {
              $art = Doctrine::getTable('ArticleLoi')->findOrCreate($json->loi, $json->article, $json->chapitre, $json->section);
              if ($json->expose) $art->expose = $json->expose;
              $art->save();
            } else if ($json->type == 'alinea') {
              $ali = Doctrine::getTable('Alinea')->findOrCreate($json->loi, $json->article, $json->alinea, $json->chapitre, $json->section);
              if ($json->texte) $ali->texte = $json->texte;
              if ($json->ref_loi) $ali->ref_loi = $json->ref_loi;
              if ($json->ref_art) $ali->ref_art = $json->ref_art;
              $ali->save();
            } else {
              echo "ERROR type : $line\n";
              continue;
            }
          }
        }
        closedir($dh);
      }
    }
  }
}
