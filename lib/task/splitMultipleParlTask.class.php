<?php

class splitMultipleParlTask extends sfBaseTask {
  protected function configure() {
    $this->namespace = 'split';
    $this->name = 'MultipleParl';
    $this->briefDescription = "Divise les interventions d'une personnalité multiple en autant d'intervenants";
    $this->addArgument('perso', sfCommandArgument::REQUIRED, 'id ou nom de la personnalite multiple');
    $this->addArgument('update', sfCommandArgument::OPTIONAL, 'Optionnel : remplir pour passer de simple affichage a modifs réelles');
    $this->addOption('env', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'prod');
    $this->addOption('app', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'frontend');
 }

  protected function execute($arguments = array(), $options = array()) {
    $manager = new sfDatabaseManager($this->configuration);
    $perso = Doctrine::getTable('Personnalite')->findOneByIdOrName($arguments['perso']);
    if (!$perso) return;
    print "FOUND perso: ".$perso->nom."\n";
    if (!preg_match('/[ ,]+(et|M(.|me)?| )+[ ,]+/', $perso->nom)) {
      print "pas une personnalité multiple\n";
      return;
    }
    $parls = array();
    foreach(preg_split('/[ ,]+(et|M(.|me)?| )+[ ,]+/', $perso->nom) as $p) {
      $parl = Doctrine::getTable('Parlementaire')->findOneByNom(trim($p));
      if ($parl) {
        print "FOUND parl : ".$parl->nom."\n";
        $parls[] = $parl;
      } else print "WARNING missing parl : ".$p."\n";
    }
    if (count($parls) < 2) {
      print "WARNING not enough parls found\n";
      return;
    }
    $parl0 = array_shift($parls);
    $parl0id = $parl0->id;
    if ($arguments['update'])
      print "Applying changes...\n";
    else print "Impacted interventions:\n";
    $interventions = Doctrine::getTable('Intervention')->findByPersonnalite($perso->id);
    if ($interventions) {
      foreach($interventions as $i) {
        print $i->id." ".$i->fonction." http://nosdeputes.fr/14/seance/".$i->seance_id.'#inter_'.$i->getMd5()."\n";
        if ($arguments['update']) {
          $ts = $i->timestamp;
          $seance = $i->getSeance();
          $typese = ($seance->type == "commission" ? $seance->getOrganisme()->nom : "hemicycle");
          $newparls = array();
          foreach($parls as $p) {
            $ts++;
	        $intervention = new Intervention();
	        $intervention->md5 = md5($i->intervention.$i->date.$seance->moment.$typese.$ts);
	        $intervention->setIntervention($i->intervention);
	        $intervention->setType($i->type);
	        $intervention->setSeanceId($seance->id);
	        $intervention->setSection($i->Section);
	        $intervention->setDate($i->date);
	        $intervention->setSource($i->source);
	        $intervention->setTimestamp($ts);
            $parlid = $p->id;
            $intervention->setParlementaire($p);
            $newparls[] = Doctrine::getTable('Parlementaire')->findOneById($parlid);
            $intervention->save();
          }
          $parls = $newparls;
          $i->setParlementaire($parl0);
          $i->save();
          $parl0 = Doctrine::getTable('Parlementaire')->findOneById($parl0id);
        }
      }
      if ($arguments['update']) {
        $query = Doctrine_Query::create()
          ->delete('Personnalite p')
          ->where('p.id = ?', $perso->id);
        if (! $query->execute())
          print "Suppression impossible de la personnalite\n";
      }
    }
  }
}

