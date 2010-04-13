<?php

class indexSolrTask extends sfBaseTask
{
  private $file_conf;
  
  private function writeState() 
  {
    $fh = fopen($this->file_conf, 'w');
    fwrite($fh, serialize($this->state));
    fclose($fh);
  }

  protected function configure()
  {
    $this->namespace = 'index';
    $this->name = 'Solr';
    $this->briefDescription = 'Index db value on solr';
    $this->addOption('env', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'test');
    $this->addOption('app', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'frontend');

    $this->file_conf = sys_get_temp_dir().DIRECTORY_SEPARATOR."reindex_slor.db";
    $this->state = array();
    if (file_exists($this->file_conf)) {
      $this->state = unserialize(file_get_contents($this->file_conf));
    }
  }
 
  protected function execute($arguments = array(), $options = array())
  {
    $dir = dirname(__FILE__).'/../../batch/commission/out/';
    $manager = new sfDatabaseManager($this->configuration);    

    $solr = new SolrConnector();


    foreach(array("Parlementaire", "QuestionEcrite", "Amendement", "Intervention") as $table) {
      while (1) {
	$q = doctrine::getTable($table)
	  ->createQuery('o')
	  ->orderBy('o.id ASC');
	if ($this->state[$table]) {
	  $q->where('o.id > ?', $this->state[$table]);
	}
	$q->limit(100);
	if (!$q->count()) {
	  echo "Count DONE\n";
	  break;
	}
	
	foreach($q->execute() as $o) {
	  echo get_class($o).' '.$o->id."\n";
	  $o->Save();
	  $this->state[$table] = $o->id;
	}
	$this->writeState();
      }
    }
  }
}
