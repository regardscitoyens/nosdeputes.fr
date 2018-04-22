<?php

class sendTestAlertTask extends sfBaseTask
{
  protected function configure()
  {
    $this->namespace = 'send';
    $this->name = 'TestAlert';
    $this->briefDescription = 'Test sendAlert';
    $this->addOption('env', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'prod');
    $this->addOption('app', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'frontend');
    $this->addOption('mailto', null, sfCommandOption::PARAMETER_OPTIONAL, 'Email that will receive the tested alert', null);
    $this->addOption('alerteid', null, sfCommandOption::PARAMETER_OPTIONAL, 'Limit the test to one alert', null);

  }

  protected function execute($arguments = array(), $options = array())
  {
    $this->configuration = sfProjectConfiguration::getApplicationConfiguration($options['app'], $options['env'], true);
    $manager = new sfDatabaseManager($this->configuration);
    $context = sfContext::createInstance($this->configuration);
    $this->configuration->loadHelpers(array('Partial', 'Url'));

    $bad_sections = Doctrine_Query::create()->select('id')->from('Section')->where('titre IS NULL OR titre LIKE "Ordre du jour%"')->fetchArray();
    $exclude_sections = array_map(function($v){ return '-id:Section/'.$v['id']; }, $bad_sections);
    $solr = new SolrConnector();
    $query = Doctrine::getTable('Alerte')->createQuery()->andWhere('(next_mail < NOW() OR next_mail IS NULL) AND confirmed = 1');
    if ($options['alerteid'])
      $query->andWhere('id = '.$options['alerteid']);
    print "SQL query for alerts: $query\n";
    foreach($query->execute() as $alerte) if (preg_match("/\w@\w/", $alerte->email)) {
      $currenttime = time();
      $date = strtotime(preg_replace('/ /', 'T', $alerte->last_mail)."Z")+1;
      $query = '('.$alerte->query.") ".join(" ", $exclude_sections)." date:[".date('Y-m-d', $date).'T'.date('H:i:s', $date)."Z TO ".date('Y-m-d', $currenttime).'T'.date('H:i:s', $currenttime)."Z]";
      foreach (explode('&', $alerte->filter) as $filtre)
        if (preg_match('/^([^=]+)=(.*)$/', $filtre, $match))
          foreach (explode(',', $match[2]) as $value) {
            if (preg_match("=", $match[2]))
              $query .= ' '.$match[1].':'.preg_replace('/=(.*)$/', '="$1"', $match[2]);
            else $query .= ' '.$match[1].':"'.$match[2].'"';
          }
      if ($alerte->no_human_query)
        $query .= " -object_name:Section";
      print "- query for alerte ".$alerte->id." to ".$alerte->email.": $query\n";
      $results = $solr->search($query, array('sort' => 'date desc', 'hl' => 'yes', 'hl.fragsize'=>500));
      if (! $results['response']['numFound']) {
        print "  => no new result\n";
        continue;
      }
      echo "sending mail to : ".$alerte->email."\n";
      echo $alerte->titre."\n";
      $text = get_partial('mail/sendAlerteTxt', array('alerte' => $alerte, 'results' => $results, 'nohuman' => $alerte->no_human_query));
      echo "$text\n";
      if ($options['mailto']) {
        $message = $this->getMailer()->compose(array('contact@regardscitoyens.org' => '"Regards Citoyens"'),
					     $options['mailto'],
					     '[NosDeputes.fr] Alerte - '.$alerte->titre);
        $message->setBody($text, 'text/plain');
        try {
          $this->getMailer()->send($message);
        } catch(Exception $e) {
          echo "ERROR: mail could not be sent ($text)\n";
        }
      }
    }
  }
}
