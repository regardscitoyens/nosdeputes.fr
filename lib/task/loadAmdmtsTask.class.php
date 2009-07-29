<?php

class loadAmdmtsTask extends sfBaseTask
{
  protected function configure()
  {
    $this->namespace = 'load';
    $this->name = 'Amdmts';
    $this->briefDescription = 'Load Amendements data';
  }
 
  protected function execute($arguments = array(), $options = array())
  {
    // your code here
    $dir = dirname(__FILE__).'/../../batch/amendements/txt/';
    $manager = new sfDatabaseManager($this->configuration);    

    if (is_dir($dir)) {
      if ($dh = opendir($dir)) {
        while (($file = readdir($dh)) !== false) {
	  print "$dir$file\n";
	  foreach(file($dir.$file) as $line) {
	    $json = json_decode($line);
	    if (!$json | !$json->legislature | !$json->numero | !$json->loi | !$json->sujet | !$json->date | !$json->auteurs) {
	      echo "ERROR json : ";
	      echo $line;
	      echo "\n";
	      continue;
	    }
	    $content_md5 = md5($json->legislature.$json->numero.$json->loi.$json->sujet.$json->texte);
            if (preg_match('%d', $json->numero)) {
                $numero = $json_numero;
                $rectif = 0;
            }
            else {
                $numero = preg_match('(%d).+', $json->numero);
  //              $rectif = parse numero de rectif
            }
            $modif = true;
            $id = $json->legislature."/amendements/".$json->loi."/".sprintf("%04d%05d",$json->loi,$numero);
	    $amdmt = Doctrine::getTable('Amendement')->find($id);
	    if (!$amdmt) {
	      $amdmt = new Amendement();
	      $amdmt->id = $id;
	      $amdmt->legislature = $json->legislature;
	      $amdmt->texteloi_id = $json->loi;
	      $amdmt->numero = $numero;
	    } elseif ($amdmt->rectif == $rectif && $amdmt->date == $json->date) {
              $modif = false;
            }
            if ($modif) {
	      $amdmt->rectif = $rectif;
              $amdmt->setAuteurs($json->auteurs);
              $amdmt->sujet = $json->sujet;
              $amdmt->texte = $json->texte;
              $amdmt->expose = $json->expose;
              $amdmt->date = $json->date;
              $amdmt->content_md5 = $json->content_md5;
            }
            $amdmt->setSort($json->sort);
            $amdmt->save();
	  }
	}
        closedir($dh);
      }
    }
  }
}