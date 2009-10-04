<?php

class loadQuestionsTask extends sfBaseTask {
  protected function configure() {
    $this->namespace = 'load';
    $this->name = 'Questions';
    $this->briefDescription = 'Load Questions data';
  }

  protected function execute($arguments = array(), $options = array()) {
  // your code here
    $dir = dirname(__FILE__).'/../../batch/questions/json/';
    $manager = new sfDatabaseManager($this->configuration);

    if (is_dir($dir)) {
      if ($dh = opendir($dir)) {
        while (($file = readdir($dh)) != false) {
          if ($file == ".." || $file == ".") continue;
          print "$dir$file\n";
          $ct_lines = 0;
          $ct_lus = 0;
          $ct_crees = 0;
          foreach(file($dir.$file) as $line) {
            $ct_lines++;
            $json = json_decode($line);
            if (!$json || !$json->source || !$json->legislature || !$json->numero || !$json->date || !$json->auteur || !$json->type || !$json->question) {
              if (!$json)
                echo "ERROR json : $line\n";
              else {
		if (!$json->source)
		  $missing = 'source';
		if (!$json->legislature)
		  $missing = 'legislature';
		if (!$json->numero)
		  $missing = 'numero';
		if (!$json->date)
		  $missing ='date';
		if (!$json->auteur)
		  $missing = 'auteur';
		if (!$json->type)
		  $missing = 'type';
		if (!$json->question)
		  $missing = 'question';
		    
		echo "ERROR json ($missing argument missing) : $line\n";		
	      }
              continue;
            }
            if (!$json->ministere_interroge || !$json->ministere_attributaire || !$json->rubrique || !$json->tete_analyse || !$json->analyse) {
              echo "ERROR json facu : $line\n";
              continue;
            }
            $ct_lus++;
            $modif = true;
            $quest = Doctrine::getTable('QuestionEcrite')->findOneBySource($json->source);
            if (!$quest) {
              $ct_crees++;
              $quest = new QuestionEcrite();
              $quest->source = $json->source;
              $quest->legislature = $json->legislature;
              $quest->numero = $json->numero;
              $quest->setAuteur($json->auteur);
            } elseif ($quest->date == $json->date && $quest->reponse != null) {
              $modif = false;
            }
            if ($modif) {
              $quest->date = $json->date;
              $quest->ministere = $json->ministere_interroge." / ".$json->ministere_attributaire;
              $quest->themes = $json->rubrique." / ".$json->tete_analyse." / ".$json->analyse;
              $quest->question = $json->question;
              $quest->reponse = $json->reponse;
              $quest->content_md5 = md5($json->legislature.$json->question);
            }
            $quest->save();
            $quest->free();
          }
          print $ct_lines." questions lues : ".$ct_lus." écrites dont ".$ct_crees." nouvelles.\n";
          unlink($dir.$file);
        }
        closedir($dh);
      }
    }
  }
}
