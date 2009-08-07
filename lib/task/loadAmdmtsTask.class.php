<?php

class loadAmdmtsTask extends sfBaseTask {
  protected function configure() {
    $this->namespace = 'load';
    $this->name = 'Amdmts';
    $this->briefDescription = 'Load Amendements data';
  }

  protected function execute($arguments = array(), $options = array()) {
  // your code here
    $dir = dirname(__FILE__).'/../../batch/amendements/txt/';
    $manager = new sfDatabaseManager($this->configuration);

    if (is_dir($dir)) {
      if ($dh = opendir($dir)) {
        while (($file = readdir($dh)) !== false) {
          if ($file == ".." || $file == ".") continue;
          print "$dir$file\n";
          $ct = 0;
          foreach(file($dir.$file) as $line) {
            $ct++;
            $json = json_decode($line);
            if (!$json || !$json->source || !$json->legislature || !$json->numero || !$json->loi || !$json->sujet || !$json->texte) {
              echo "ERROR json : $line\n";
              continue;
            }
            $modif = true;
            $amdmt = Doctrine::getTable('Amendement')->findOneBySource($json->source);
            if (!$amdmt) {
              $amdmt = new Amendement();
              $amdmt->source = $json->source;
              $amdmt->legislature = $json->legislature;
              $amdmt->texteloi_id = $json->loi;
              $amdmt->addTag('loi:numero_loi='.$amdmt->texteloi_id);
              $amdmt->numero = $json->numero;
              $amdmt->addTag('loi:amendement='.$amdmt->numero);
            } elseif ($amdmt->rectif == $json->rectif && $amdmt->date == $json->date) {
              $modif = false;
            }
            if ($modif) {
              $amdmt->rectif = $json->rectif;
              if ($json->date)
                $amdmt->date = $json->date;
              if ($json->fin_serie) {
                $n = $amdmt->numero + 1;
                while ($n <= $json_fin_serie) {
                  $amdmt->addTag('loi:amendement='.$n);
                }
              }
              if ($json->parent) {
                $amdmt->addTag('loi:suramendement='.$json->parent);
              }
              $amdmt->sujet = $json->sujet;
              $amdmt->texte = $json->texte;
              if ($json->expose)
                $amdmt->expose = $json->expose;
              $amdmt->content_md5 = md5($json->legislature.$json->loi.$json->sujet.$json->texte);
            }
            if ($json->sort)
              $amdmt->sort = $json->sort;
            elseif (!$amdmt->sort)
                $amdmt->sort = "Indéfini";
            if ($json->auteurs) { /// remettre dans modif?
              $amdmt->signataires = $json->auteurs;
              $amdmt->setAuteurs($json->auteurs);
            } else {
              if (!$json->sort || !preg_match('/(irrecevable|retir)/i', $json->sort)) {
                echo "ERROR json auteurs missing : $line\n";
                continue;
              }
            }
            $amdmt->save();
            $amdmt->free();
          }
          print $ct." amdmts"."\n";
        }
        closedir($dh);
      }
    }
  }
}