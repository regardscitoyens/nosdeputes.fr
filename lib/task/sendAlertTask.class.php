<?php

class sendAlertTask extends sfBaseTask
{
  protected function configure()
  {
    $this->namespace = 'send';
    $this->name = 'Alert';
    $this->briefDescription = 'send alerts';
    $this->addOption('env', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'prod');
    $this->addOption('app', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'frontend');
  }
 
  protected static $period = array('HOUR' => 3600, 'DAY' => 86400, 'WEEK' => 604800, 'MONTH' => 2592000);

  protected function execute($arguments = array(), $options = array())
  {
    $this->configuration = sfProjectConfiguration::getApplicationConfiguration($options['app'], $options['dev'], true);
    $manager = new sfDatabaseManager($this->configuration);
    $context = sfContext::createInstance($this->configuration);
    $this->configuration->loadHelpers(array('Partial', 'Url'));
    
    $solr = new SolrConnector();
    $query = Doctrine::getTable('Alerte')->createQuery('a')->where('next_mail < NOW()')->andWhere('confirmed = 1');
	echo $query->getSqlQuery();
    foreach($query->execute() as $alerte) {
      echo "Alerte\n";
      $date = strtotime(preg_replace('/ /', 'T', $alerte->last_mail)."Z")-3600*2+1;
      $query = $alerte->query." date:[".date('Y-m-d', $date).'T'.date('H:i:s', $date)."Z TO ".date('Y-m-d').'T'.date('H:i:s')."Z]";
      $results = $solr->search($query, array('sort' => 'date desc', 'hl' => 'yes', 'hl.fragsize'=>500));
      $alerte->next_mail = date('Y-m-d H:i:s', time() + self::$period[$alerte->period]);
      if (! $results['response']['numFound']) {
	echo "query « $query » returned no results\n";
//	$alerte->save();
	continue;
      }
      echo "sending mail to : ".$alerte->email."\n";
      $message = $this->getMailer()->compose(array('no-reply@nosdeputes.fr' => "Regards Citoyens"), 
//					     $alerte->email,
					     "tangui@tangui.eu.org",
					     '[NosDeputes.fr] Alerte - '.$alerte->titre);
      echo $alerte->titre."\n";
      $text = get_partial('mail/sendAlerteTxt', array('alerte' => $alerte, 'results' => $results['response']));
      $message->setBody($text, 'text/plain');
      try {
	$this->getMailer()->send($message);
	echo $message;
	$alerte->last_mail = preg_replace('/T/', ' ', preg_replace('/Z/', '', $results['response']['docs'][$results['response']['numFound'] -1]['date']));
//	$alerte->save();
      }catch(Exception $e) {
	echo "ERROR: mail could not be sent ($text)\n";
      }
    }
  }
  
}
