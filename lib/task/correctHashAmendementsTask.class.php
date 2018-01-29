<?php

class correctHashAmendementsTask extends sfBaseTask
{
  protected function configure()
  {
    $this->namespace = 'correct';
    $this->name = 'HashAmendements';
    $this->briefDescription = 'Correct Hash Amendements';
    $this->addOption('startid', null, sfCommandOption::PARAMETER_OPTIONAL, 'First id to start with', 0);
    $this->addOption('env', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'test');
    $this->addOption('app', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'frontend');
  }


  protected function updateAmendement($nextid) {
    $done = 0;
    foreach(Doctrine_Query::create()->from('Amendement a')
     ->select('a.id, a.legislature, a.texteloi_id, a.sujet, a.texte')
     ->where("id > ".$nextid)
     ->orderBy('id')
     ->limit(100)
     ->execute() as $a) {
      $md5 = md5($a->legislature.$a->texteloi_id.$a->sujet.$a->texte);
      if ($md5 !== $a->content_md5) {
        $a->content_md5 = $md5;
        $a->save();
        print "UPDATE ".$a->id."...\n";
      }
      $nextid = $a->id;
      print "$nextid\n";
      $done++;
    }
    if (!$done) return $done;
    return $nextid;
  }

  protected function execute($arguments = array(), $options = array())
  {
    $dir = dirname(__FILE__).'/../../batch/depute/out/';
    $manager = new sfDatabaseManager($this->configuration);
    $nextid = $options["startid"];

    while (1) {
      $nextid = $this->updateAmendement($nextid);
      if(!$nextid)
        break;
    }
    echo "\n";
  }
}
