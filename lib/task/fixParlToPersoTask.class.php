<?php

class fixParlToPersoTask extends sfBaseTask {
  protected function configure() {
    $this->namespace = 'fix';
    $this->name = 'ParlToPerso';
    $this->briefDescription = "Réaffecte les interventions d'un parlementaire à une personnalité (cas de ministres en mandat)";
    $this->addArgument('parl', sfCommandArgument::REQUIRED, 'id ou nom du parlementaire');
    $this->addArgument('perso', sfCommandArgument::REQUIRED, 'id ou nom du personnalite');
    $this->addArgument('seance', sfCommandArgument::OPTIONAL, 'Optionnel : remplir pour activer modifs réelles sur seance indiquée');
    $this->addArgument('fonction', sfCommandArgument::OPTIONAL, 'Optionnel : remplir pour donner une fonction au parlementaire sur les interventions modifiées');
    $this->addOption('env', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'prod');
    $this->addOption('app', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'frontend');
 }

  protected function execute($arguments = array(), $options = array()) {
    $this->configuration = sfProjectConfiguration::getApplicationConfiguration($options['app'], $options['env'], true);
    $manager = new sfDatabaseManager($this->configuration);
    sfContext::createInstance($this->configuration);
    $parl = Doctrine::getTable('Parlementaire')->findOneByIdOrName($arguments['parl']);
    $perso = Doctrine::getTable('Personnalite')->findOneByIdOrName($arguments['perso']);
    if (!$perso || !$parl) return;
    $fct = ($arguments['fonction'] ? $arguments['fonction'] : (preg_match('/, (.+)$/', $perso->nom, $m) ? $m[1] : null));
    print "FOUND parl : ".$parl->nom."\n";
    print "FOUND perso: ".$perso->nom." ".$fct."\n";
    if ($arguments['seance'])
      print "Applying changes...\n";
    else print "Impacted interventions:\n";
    $interventions = Doctrine::getTable('Intervention')->findByParlementaire($parl->id);
    if ($interventions) {
      foreach($interventions as $i) {
        print $i->id." ".$i->fonction." http://nosdeputes.fr/".sfConfig::get('app_legislature')."/seance/".$i->seance_id.'#inter_'.$i->getMd5()."\n";
        if ($arguments['seance'] && $i->seance_id == $arguments['seance']) {
          $i->setPersonnalite($perso);
          if ($fct) $i->setFonction($fct);
          $i->save();
          $perso = Doctrine::getTable('Personnalite')->findOneByIdOrName($arguments['perso']);
        }
      }
      if ($arguments['seance']) {
        $query = Doctrine_Query::create()
          ->delete('Presence p')
          ->where('p.parlementaire_id = ?', $parl->id)
          ->andWhere('p.seance_id = ?', $arguments['seance']);
        if (! $query->execute())
          print "Suppression impossible de la présence du parlementaire à cette séance\n";
      }
    }
  }
}

