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
          print "$dir$file\n";
          $ct = 0;
          foreach(file($dir.$file) as $line) {
            $ct++;
            $json = json_decode($line);
            if (!$json || !$json->legislature || !$json->numero || !$json->loi || !$json->sujet || !$json->date || !$json->sujet || !$json->sujet) {
              echo "ERROR json : ";
              echo "$line";
              echo "\n";
              continue;
            }
            $modif = true;
            $id = $json->legislature."/amendements/".$json->loi."/".sprintf("%04d%05d",$json->loi,$json->numero);
            $amdmt = Doctrine::getTable('Amendement')->find($id);
            if (!$amdmt) {
              $amdmt = new Amendement();
              $amdmt->id = $id;
              $amdmt->source = "http://www.assemblee-nationale.fr/".$id;
              $amdmt->legislature = $json->legislature;
              $amdmt->texteloi_id = $json->loi;
              $amdmt->numero = $json->numero;
            } elseif ($amdmt->rectif == $json->rectif && $amdmt->date == $json->date) {
              $modif = false;
            } // else print "/".$amdmt->rectif."/".$json->rectif."///".$amdmt->date."/".$json->date."/\n";
            if ($json->sort)
              $amdmt->setSort($json->sort);
 //           if ($modif) {
              $amdmt->rectif = $json->rectif;
              if ($json->auteurs) {         /// remettre dans modif
                $amdmt->setAuteurs($json->auteurs);
              }
              else {
                if (!$json->sort || !preg_match('/irrecevable/i', $json->sort)) {
                  echo "ERROR auteurs missing : $line\n";
                  continue;
                }
              }
              $amdmt->sujet = $json->sujet;
              if ($json->texte)
                $amdmt->texte = $json->texte;
              if ($json->expose)
                $amdmt->expose = $json->expose;
              $amdmt->date = $json->date;
              $amdmt->content_md5 = md5($json->legislature.$json->numero.$json->loi.$json->sujet.$json->texte);
 //           }

// ﻿addTag('loi:amendement=123')
// ﻿addTag('loi:numero_loi=123')
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