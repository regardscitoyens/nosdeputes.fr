<?php

class loadQuestionsTask extends sfBaseTask {
  protected function configure() {
    $this->namespace = 'load';
    $this->name = 'Questions';
    $this->briefDescription = 'Load Questions data';
    $this->addOption('env', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'test');
    $this->addOption('app', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'frontend');
  }

  protected function execute($arguments = array(), $options = array()) {
  // your code here
    $dir = dirname(__FILE__).'/../../batch/questions/json/';
    $manager = new sfDatabaseManager($this->configuration);

    if (is_dir($dir)) {
      if ($dh = opendir($dir)) {
	$ct_lines = 0;
	$ct_lus = 0;
	$ct_crees = 0;
        while (($file = readdir($dh)) != false) {
          if ($file == ".." || $file == ".") continue;
          foreach(file($dir.$file) as $line) {
            $ct_lines++;
            $json = json_decode($line);
            if (!$json || !$json->source || !$json->legislature || !$json->numero || !$json->date_question || !$json->auteur || !$json->type || !$json->question) {
              if (!$json)
                echo "ERROR json : $line\n";
              else {
		if (!$json->source)
		  $missing = 'source';
		if (!$json->legislature)
		  $missing = 'legislature';
		if (!$json->numero)
		  $missing = 'numero';
		if (!$json->date_question)
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
            if (!$json->ministere_interroge || !$json->ministere_attribue || !$json->rubrique || (!$json->tete_analyse && !$json->analyse)) {
              echo "ERROR json facu : $line\n";
              continue;
            }
            $ct_lus++;
            $quest = Doctrine::getTable('QuestionEcrite')->findOneBySource($json->source);
            if (!$quest) {
              $ct_crees++;
              $quest = new QuestionEcrite();
              $quest->source = $json->source;
              $quest->legislature = $json->legislature;
              $quest->numero = $json->numero;
            }
            $quest->setAuteur($json->auteur);  // déplacé de la zone de création de nouvelles questions ci-dessus pour permettre correction de l'auteur au besoin, potentiellement lourd, à revert si besoin
            if (!$quest->reponse || $quest->reponse === "") {
              $quest->date = $json->date_question;
              $quest->ministere = $json->ministere_interroge." / ".$json->ministere_attribue;
              $quest->themes = $json->rubrique;
              if ($json->tete_analyse) $quest->themes .= " / ".$json->tete_analyse;
              if ($json->analyse) $quest->themes .= " / ".$json->analyse;
              $quest->question = $json->question;
              $quest->content_md5 = md5($json->legislature.$json->question);
              if ($json->date_retrait) {
                $quest->date_cloture = $json->date_retrait;
                if ($json->motif_retrait)
                  $quest->motif_retrait = $json->motif_retrait;
              } else if ($json->date_reponse) {
                $quest->date_cloture = $json->date_reponse;
              }
            }
            $quest->reponse = $json->reponse;
            $quest->save();
            $quest->free();
          }
          unlink($dir.$file);
        }
        closedir($dh);
	if ($ct_crees) print "$dir\n".$ct_lines." questions lues : ".$ct_lus." écrites dont ".$ct_crees." nouvelles.\n";
      }
    }
  }
}
