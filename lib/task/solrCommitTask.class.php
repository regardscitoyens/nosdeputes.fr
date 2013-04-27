<?php

class solrCommitTask extends sfBaseTask
{
  private $file_conf;
  
  protected function configure()
  {
    $this->namespace = 'solr';
    $this->name = 'Commit';
    $this->briefDescription = 'Index db value on solr';
    $this->addOption('env', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'prod');
    $this->addOption('app', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'frontend');
  }

  protected function execute($arguments = array(), $options = array())
  {

    $this->configuration = sfProjectConfiguration::getApplicationConfiguration($options['app'], $options['env'], true);
    $manager = new sfDatabaseManager($this->configuration);    
    $solr = new SolrConnector();
    $solr->commit(1, 1);
  }
  
}
