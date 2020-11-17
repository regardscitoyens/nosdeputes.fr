<?php

class fixTagScrutinsInterventionsTask extends sfBaseTask {

  protected function configure() {
    $this->namespace = 'fix';
    $this->name = 'TagScrutinsInterventions';
    $this->briefDescription = "Force la recherche et le tagging de l'intervention pour chaque scrutin";
    $this->addOption('env', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'test');
    $this->addOption('app', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'frontend');
  }

  protected function execute($arguments = array(), $options = array()) {
    $manager = new sfDatabaseManager($this->configuration);
    $scrutins = Doctrine::getTable('Scrutin')->createQuery('s')->execute();
    foreach ($scrutins as $s) {
      echo "- Scrutin n°$s->numero... ";
      try {
        $inter = $s->tagIntervention();
        echo " -> https://www.nosdeputes.fr/".myTools::getLegislature()."/seance/$inter  \n";
      } catch(Exception $e) {
        echo "ERREUR Scrutin n°$s->numero (tag interventions) : {$e->getMessage()}\n";
        continue;
      }
    }
  }
}

