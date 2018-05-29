<?php

class fixNbCommentairesTask extends sfBaseTask {
  private $file_conf;

  private function writeState()
  {
    $fh = fopen($this->file_conf, 'w');
    fwrite($fh, serialize($this->state));
    fclose($fh);
  }

  protected function configure() {
    $this->namespace = 'fix';
    $this->name = 'NbCommentaires';
    $this->briefDescription = 'Correct Titres et Objects des commentaires';
    $this->addOption('env', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'test');
    $this->addOption('app', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'frontend');
    $this->file_conf = sys_get_temp_dir().DIRECTORY_SEPARATOR."reindex_solr.db";
    $this->state = array();
    if (file_exists($this->file_conf)) {
      $this->state = unserialize(file_get_contents($this->file_conf));
    }
 }

  protected function execute($arguments = array(), $options = array()) {
    $manager = new sfDatabaseManager($this->configuration);
    foreach(array("Parlementaire", "Section", "Intervention", "Amendement", "QuestionEcrite", "Texteloi") as $table) {
      while (1) {
	    $q = Doctrine::getTable($table)
	      ->createQuery('o')
          ->andWhere('o.nb_commentaires > 0')
	      ->orderBy('o.id ASC');
	    if (isset($this->state[$table]))
	      $q->andWhere('o.id > ?', $this->state[$table]);
	    if (!$q->count()) {
	      echo "Count DONE\n";
	      break;
	    }
	    $q->limit(100);
	    foreach($q->execute() as $o) {
	      echo get_class($o).' '.$o->id."\n";
	      $o->updateNbCommentaires();
          $o->save();
	      $this->state[$table] = $o->id;
	    }
	    $this->writeState();
      }
    }
    unlink($this->file_conf);
  }
}
