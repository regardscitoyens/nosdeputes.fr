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
          $precedent = '';
          $refcode = '';
          if (isset($oldart)) unset($oldart);
          if ($file == ".." || $file == ".") continue;
          foreach(file($dir.$file) as $line) {
            $json = json_decode($line);
            if (!$json || !$json->type) {
              print $json->type;
              echo "ERROR json : $line\n";
              continue;
            }
            if (isset($json->expose)) $json->expose = preg_replace("/<a\shref='([^']+)'>/", '<a href="\1">', $json->expose);
            if (isset($json->texte)) $json->texte = preg_replace("/<a\shref='([^']+)'>/", '<a href="\1">', $json->texte);
            if ($json->type == 'loi') {
              $loi = Doctrine::getTable('TitreLoi')->findLoiOrCreate($json->loi);
              if ($json->source) $loi->source = $json->source;
              if ($json->titre) $loi->titre = $json->titre;
              if ($json->expose) $loi->expose = $json->expose;
              if ($json->date) $loi->date = $json->date;
              if ($json->auteur) $loi->setAuteur($json->auteur);
              $loi->save();
            } else if ($json->type == 'section') {
              $sec = Doctrine::getTable('TitreLoi')->findLevelOrCreate($json->loi, $json->level, array($json->level1, $json->level2, $json->level3, $json->level4), $json->leveltype);
              if ($json->titre) $sec->titre = $json->titre;
              if ($json->expose) $sec->expose = $json->expose;
              $sec->save();
            } else if ($json->type == 'article') {
              $art = Doctrine::getTable('ArticleLoi')->findOrCreate($json->loi, $json->article, array($json->level1, $json->level2, $json->level3, $json->level4));
              if ($json->expose && $json->expose != '') $art->expose = $json->expose;
              if ($json->ordre && $json->ordre != '') {
                $art->ordre = $json->ordre;
                if (isset($oldart)) {
                  $oldart->suivant = $art->slug;
                  $oldart->save();
                  $art->precedent = $oldart->slug;
                }
                $art->save();
                $oldart = $art;
              } else $art->save();
            } else if ($json->type == 'alinea') {
              $ali = Doctrine::getTable('Alinea')->findOrCreate($json->loi, $json->article, $json->alinea, array($json->level1, $json->level2, $json->level3, $json->level4));
              if ($json->alinea == 1) {
                if ($refcode) $oldcode = $refcode;
                $refcode = '';
              }
              if (!$refcode && preg_match('/ même (code|loi|ordonnance)/', $json->texte))
                $refcode = $oldcode;
              if ($json->texte) $refcode = $ali->setTexteCode($json->texte, $refcode);
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
