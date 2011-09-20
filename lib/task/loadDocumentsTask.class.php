<?php

class loadDocumentsTask extends sfBaseTask {
  protected function configure() {
    $this->namespace = 'load';
    $this->name = 'Documents';
    $this->briefDescription = 'Load Documents data';
    $this->addOption('env', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'test');
    $this->addOption('app', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'frontend');
  }

  protected function execute($arguments = array(), $options = array()) {
    $dir = dirname(__FILE__).'/../../batch/documents/out/';
    $manager = new sfDatabaseManager($this->configuration);

    if (is_dir($dir)) {
      if ($dh = opendir($dir)) {
        while (($file = readdir($dh)) != false) {
          if ($file == ".." || $file == "." || $file == ".svn") continue;
          foreach(file($dir.$file) as $line) {
	    echo "\n$dir$file ... ";
            $json = json_decode($line);
            if (!$json )
	    {
              echo "ERROR json : \n";
              continue;
            }
	    if (!$json->source)
	      {echo "ERROR source : \n"; continue;}
	    if (!$json->id)
	      {echo "ERROR id : \n"; continue;}
	    if (!$json->numero)
	      {echo "ERROR numero : \n"; continue;}
	    if(!$json->date)
	      {echo "ERROR date : \n"; continue;}
	    if (!$json->type)
	      {echo "ERROR type : \n"; continue;}
            $doc = Doctrine::getTable('Texteloi')->find($json->id);
            if (!$doc) {
              $doc = new Texteloi();
              $doc->id = $json->id;
              $doc->source = $json->source;
              $doc->numero = $json->numero;
              if ($json->annexe != "")
                $doc->annexe = $json->annexe;
              $doc->date = $json->date;
              $doc->type = $json->type;
            }
            if ($json->dossier)
              $doc->setDossier($json->dossier);
            if ($json->type_details)
              $doc->type_details = $json->type_details;
            if ($json->titre)
              $doc->titre = $json->titre;
            if ($json->auteurs)
              $doc->setAuteurs($json->auteurs);
            if ($json->motscles)
              foreach (explode('.', $json->motscles) as $tag)
                if (strlen($tag) <= 50)
                  $doc->addTag($tag);
	    if ($json->contenu)
	      $doc->setContenu($json->contenu);
            $doc->save();
	    echo " DONE";
          }
	  unlink($dir.$file);
        }
        closedir($dh);
        echo "\n";
      }
    }
  }
}
