<?php

class fixPersoToParlTask extends sfBaseTask {
  protected function configure() {
    $this->namespace = 'fix';
    $this->name = 'PersoToParl';
    $this->briefDescription = "Supprime une personnalité et réaffecte ses interventions (et présences) à un parlementaire";
    $this->addArgument('perso', sfCommandArgument::REQUIRED, 'id ou nom de la personnalite');
    $this->addArgument('parl', sfCommandArgument::REQUIRED, 'id ou nom du parlementaire');
    $this->addArgument('seance', sfCommandArgument::OPTIONAL, 'Optionnel : remplir pour activer modifs réelles sur seance indiquée');
    $this->addArgument('fonction', sfCommandArgument::OPTIONAL, 'Optionnel : remplir pour donner une fonction au parlementaire sur les interventions modifiées');
    $this->addOption('env', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'prod');
    $this->addOption('app', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'frontend');
 }

  protected function execute($arguments = array(), $options = array()) {
    $manager = new sfDatabaseManager($this->configuration);
    $perso = Doctrine::getTable('Personnalite')->findOneByIdOrName($arguments['perso']);
    $parl = Doctrine::getTable('Parlementaire')->findOneByIdOrName($arguments['parl']);
    if (!$perso || !$parl) return;
    $fct = ($arguments['fonction'] ? $arguments['fonction'] : (preg_match('/, (.+)$/', $perso->nom, $m) ? $m[1] : null));
    print "FOUND perso: ".$perso->nom." ".$fct."\n";
    print "FOUND parl : ".$parl->nom."\n";
    if ($arguments['seance'])
      print "Applying changes...\n";
    else print "Impacted interventions:\n";
    $interventions = Doctrine::getTable('Intervention')->findByPersonnalite($perso->id);
    if ($interventions) {
      $rmperso = true;
      foreach($interventions as $i) {
        print $i->id." ".$i->fonction." http://nosdeputes.fr/14/seance/".$i->seance_id.'#inter_'.$i->getMd5()."\n";
        if ($arguments['seance'] && $i->seance_id == $arguments['seance']) {
          $i->setParlementaire($parl);
          if ($fct) $i->setFonction($fct);
          $i->save();
          $parl = Doctrine::getTable('Parlementaire')->findOneByIdOrName($arguments['parl']);
        }
        if ($i->seance_id != $arguments['seance']) {
          $rmperso = false;
        }
      }
      if ($arguments['seance'] && $rmperso) {
        $query = Doctrine_Query::create()
          ->delete('Personnalite p')
          ->where('p.id = ?', $perso->id);
        if (! $query->execute())
          print "Suppression impossible de la personnalite\n";
      }
    }
  }
}

