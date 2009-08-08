<?php

class loadAmdmtsTask extends sfBaseTask {
  protected function configure() {
    $this->namespace = 'load';
    $this->name = 'Amdmts';
    $this->briefDescription = 'Load Amendements data';
  }

  protected function execute($arguments = array(), $options = array()) {
  // your code here
    $dir = dirname(__FILE__).'/../../batch/amendements/input/';
    $manager = new sfDatabaseManager($this->configuration);

    if (is_dir($dir)) {
      if ($dh = opendir($dir)) {
        while (($file = readdir($dh)) !== false) {
          if ($file == ".." || $file == ".") continue;
          print "$dir$file\n";
          $ct_lines = 0;
          $ct_lus = 0;
          $ct_crees = 0;
          foreach(file($dir.$file) as $line) {
            $ct_lines++;
            $json = json_decode($line);
            if (!$json || !$json->source || !$json->legislature || !$json->numero || !$json->loi || !$json->sujet || !$json->texte) {
              echo "ERROR json : $line\n";
              continue;
            }
            $ct_lus++;
            $modif = true;
            $amdmt = Doctrine::getTable('Amendement')->findOneBySource($json->source);
            if (!$amdmt) {
              $ct_crees++;
              $amdmt = new Amendement();
              $amdmt->source = $json->source;
              $amdmt->legislature = $json->legislature;
              $amdmt->texteloi_id = $json->loi;
              $amdmt->addTag('loi:numero_loi='.$amdmt->texteloi_id);
              $amdmt->numero = $json->numero;
            } elseif ($amdmt->rectif == $json->rectif && $amdmt->date == $json->date) {
              $modif = false;
            }
            if ($modif) {
              $amdmt->rectif = $json->rectif;
              if ($json->date)
                $amdmt->date = $json->date;
              if ($json->serie) {
                if (preg_match('/,/', $json->serie)) {
                  $arr = preg_split('/,/', $json->serie);
                  foreach ($arr as $gap_stri) {
                    $gap = preg_split('/-/', $gap_stri);
                    for ($n = $gap[0]; $n <= $gap[1]; $n++) 
                      $amdmt->addTag('loi:amendement='.$n);
                  }
                } else {
                  $gap = preg_split('/-/', $json->serie);
                  for ($n = $gap[0]; $n <= $gap[1]; $n++)
                    $amdmt->addTag('loi:amendement='.$n);
                }
              } else $amdmt->addTag('loi:amendement='.$amdmt->numero);
              if ($json->parent)
                $amdmt->addTag('loi:sous_amendement_de='.$json->parent);
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
                $amdmt->free();
                continue;
              }
            }
            $amdmt->save();
            $amdmt->free();
          }
          print $ct_lines." amendements lus : ".$ct_lus." écrits dont ".$ct_crees." nouveaux.\n";
          unlink($dir.$file);
        }
        closedir($dh);
      }
    }
  }
}