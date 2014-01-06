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

    $ct_lines = 0;
    $ct_lus = 0;
    $ct_crees = 0;
    if (is_dir($dir)) {
      if ($dh = opendir($dir)) {
        while (($file = readdir($dh)) != false) {
          if ($file == ".." || $file == ".") continue;
          foreach(file($dir.$file) as $line) {
            $ct_lines++;
            $json = json_decode($line);
            if (!$json || !$json->source || !$json->legislature || !$json->numero || !$json->titre || !$json->date_question || !$json->auteur || !$json->type || !$json->question || !$json->ministere) {
              if (!$json)
                echo "ERROR json : $line\n";
              else {
		if (!$json->source)
		  $missing = 'source';
		if (!$json->legislature)
		  $missing = 'legislature';
		if (!$json->numero)
		  $missing = 'numero';
                if (!$json->titre)
                  $missing = 'titre';
		if (!$json->date_question)
		  $missing ='date';
		if (!$json->auteur)
		  $missing = 'auteur';
		if (!$json->type)
		  $missing = 'type';
		if (!$json->question)
		  $missing = 'question';
                if (!$json->ministere)
                  $missing = 'ministere';
		echo "ERROR json ($missing argument missing) : $line\n";
	      }
              continue;
            }
            $ct_lus++;
            $quest = Doctrine::getTable('Question')->findOneBySource($json->source);
            if (!$quest) {
              $ct_crees++;
              $quest = new Question();
              $quest->source = $json->source;
              $annee = preg_replace('#^.*senat\.fr/questions/base/20(\d\d)/.*$#', '\\1', $quest->source);
              $quest->legislature = $json->legislature;
              $quest->type = $json->type;
              $quest->numero = $json->numero;
              $quest->numero = preg_replace('/^(\d+)([a-z])$/i', $annee.'\\2\\1', $quest->numero);
            }
            $quest->setAuteur($json->auteur);  // déplacé de la zone de création de nouvelles questions ci-dessus pour permettre correction de l'auteur au besoin, potentiellement lourd, à revert si besoin
            $quest->date = $json->date_question;
            if (!$quest->reponse || $quest->reponse === "" || preg_match("/réponse n'est pas disponible à ce jour/", $quest->reponse)) {
              $quest->ministere = $json->ministere;
              if (!$json->rappel)
                $json->rappel = -1;
              if (!$json->transformee_en)
                $json->transformee_en = -1;
              $quest->titre = $json->titre;
              $quest->setQuestion($json->question, $json->rappel, $json->transformee_en);
              $quest->content_md5 = md5($json->legislature.$json->question);
              if ($json->motif_retrait)
                $quest->motif_retrait = $json->motif_retrait;
            }
            if ($json->date_reponse)
              $quest->date_cloture = $json->date_reponse;
            $quest->setReponse($json->reponse);
            $quest->save();
          }
          unlink($dir.$file);
        }
        if ($ct_crees) print "$ct_lines questions lues : $ct_lus mises-à-jour dont $ct_crees nouvelles.\n";
        closedir($dh);
      }
    }
  }
}
