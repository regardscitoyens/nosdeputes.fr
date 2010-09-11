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
//print $file."\n";
          foreach(file($dir.$file) as $line) {
            $json = json_decode($line);
            if (!$json || !$json->source || !$json->legislature || !$json->id || !$json->numero || !$json->date_depot || !$json->dossier || !$json->type) {
              echo "ERROR json : $line\n";
              continue;
            }
            $doc = Doctrine::getTable('TexteLoi')->find($json->id);
            if (!$doc) {
              $doc = new TexteLoi();
              $doc->id = $json->id;
              $doc->source = $json->source;
              $doc->legislature = $json->legislature;
              $doc->numero = $json->numero;
              if ($json->annexe)
                $doc->annexe = $json->annexe;
              $doc->date = $json->date_depot;
              $doc->type = $json->type;
              $doc->save();
            }
            if ($json->date_publi)
              $doc->date = $json->date_publi;
            $doc->setDossier($json->dossier);
            if ($json->type_details)
              $doc->type_details = $json->type_details;
            if ($json->titre)
              $doc->titre = $json->titre;
            if ($json->categorie)
              $doc->categorie = strtolower($json->categorie);
            if ($json->auteurs)
              $doc->setAuteurs($json->auteurs);
            if ($json->motscles)
              foreach (explode('.', $json->motscles) as $tag)
                $doc->addTag($tag);
            $doc->save();
            $doc->free();
          }
          unlink($dir.$file);
        }
        closedir($dh);
      }
    }
  }
}
