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
    $this->addOption('env', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'prod');
    $this->addOption('app', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'frontend');
    $this->addOption('removeAll', null, sfCommandOption::PARAMETER_OPTIONAL, 'Drop solr database (=no|yes no default)', 'no');
    $this->addOption('all', null, sfCommandOption::PARAMETER_OPTIONAL, 'Reindex all the database (=no|yes no default)', 'no');
    $this->addOption('pages', null, sfCommandOption::PARAMETER_OPTIONAL, 'Index static pages (=no|yes no default)', 'no');
    $this->addOption('removePages', null, sfCommandOption::PARAMETER_OPTIONAL, 'remove indexed static pages(=no|yes no default)', 'no');
    $this->addOption('verbose', null, sfCommandOption::PARAMETER_OPTIONAL, 'Print the indexed object ID (=no|yes no default)', 'no');

    $this->file_conf = sys_get_temp_dir().DIRECTORY_SEPARATOR."reindex_slor.db";
    $this->state = array();
    if (file_exists($this->file_conf)) {
      $this->state = unserialize(file_get_contents($this->file_conf));
    }
  }

  protected function removeNonObjectPages($solr) {
    $array = NonObjectPage::getElements();
    foreach ($array as $k => $v) {
      $solr->deleteLuceneRecord('NonObjectPage/'.$k);
    }
  }
  protected function indexNonObjectPages($solr) {
    $array = NonObjectPage::getElements();
    foreach ($array as $k => $v) {
      $json = new stdClass();
      $json->id = 'NonObjectPage/'.$k;
      $json->object_name = 'NonObjectPage';
      $json->object_id = $k;
      $weight = $v['weight'];
      if (!$weight) {
	$weight = 1;
      }
      $json->title->content = $v['title'];
      $json->title->weight  = $weight * 1.2;
      if (isset($a['description'])) {
	$json->description->content = $v['description'];
	$json->description->weight  = $weight;
      }
      $solr->updateLuceneRecord($json);
    }
  }
 
  protected function execute($arguments = array(), $options = array())
  {
    $this->configuration = sfProjectConfiguration::getApplicationConfiguration($options['app'], $options['env'], true);
    $manager = new sfDatabaseManager($this->configuration);    

    $solr = new SolrConnector();

    if ($options['removeAll'] == 'yes') {
      $solr->deleteAll();
    }

    if ($options['removePages'] == 'yes') {
      $this->removeNonObjectPages($solr);
      return ;
    }

    if ($options['pages'] == 'yes') {
      $this->indexNonObjectPages($solr);
      return ;
    }

    if ($options['all'] == 'no') {
      $solr->updateFromCommands($options['verbose'] == 'yes');
      return;
    }

    foreach(array("Parlementaire", "Organisme", "Section", "Intervention", "Amendement", "QuestionEcrite", "Citoyen", "Commentaire", "Texteloi") as $table) {
      while (1) {
	$q = Doctrine::getTable($table)
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
	$solr->updateFromCommands($options['verbose'] == 'yes');
	$this->writeState();
      }
    }
    //    unlink($this->file_conf);
  }
  
}
