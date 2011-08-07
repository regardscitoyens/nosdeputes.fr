<?php

class removeExtraParlAmdmtsTask extends sfBaseTask {
  protected function configure() {
    $this->namespace = 'remove';
    $this->name = 'ExtraParlAmdmts';
    $this->briefDescription = 'Remove double ParlementaireAmendements entities';
    $this->addOption('env', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'test');
    $this->addOption('app', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'frontend');
 }

  protected function execute($arguments = array(), $options = array()) {
    $manager = new sfDatabaseManager($this->configuration);
    $doublons = Doctrine::getTable('ParlementaireAmendement')->createQuery('pa')
      ->select('amendement_id, parlementaire_id, count(*) as ct')
      ->groupBy('amendement_id, parlementaire_id')
      ->fetchArray();
    if ($doublons) foreach($doublons as $d) if ($d['ct'] > 1) {
      print "\n".'Amendement '.$d['amendement_id'].' associé '.$d['ct'].' fois au député '.$d['parlementaire_id']."\n";
      $ids = Doctrine::getTable('ParlementaireAmendement')->createQuery('pa')
        ->select('id')
        ->where('amendement_id = ?', $d['amendement_id'])
        ->andWhere('parlementaire_id = ?', $d['parlementaire_id'])
        ->orderBy('id')
        ->fetchArray();
      $n = 0;
      foreach ($ids as $id) {
        $n++;
        if ($n == 1) {
          print "keep PA #".$id['id']." // remove ";
          continue;
        }
        print "#".$id['id']." ";
        Doctrine_Query::create()->delete('ParlementaireAmendement pa')->where('pa.id = ?', $id['id'])->execute();
      }
    }
  }
}

