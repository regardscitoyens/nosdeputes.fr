<?php

class setSessionTask extends sfBaseTask {
  protected function configure() {
    $this->namespace = 'set';
    $this->name = 'Session';
    $this->briefDescription = 'Load Sessions for Seances';
  }

  protected function execute($arguments = array(), $options = array()) {
    $semaines = array();

    $manager = new sfDatabaseManager($this->configuration);
    $sessions = Doctrine::getTable('Seance')->createQuery('s')->select('session, min(date)')->groupBy('session')->where('session LIKE ?', '2%')->orderBy('min(date) DESC')->fetchArray();
    $serialize = array();
    foreach($sessions as $s) {
      $serialize[$s['session']] = $s['min'];
    }
    $var = Doctrine::getTable('VariableGlobale')->findOneByChamp('session');
    if (!$var) {
      $var = new VariableGlobale();
      $var->champ = 'session';
    }
    $var->value = serialize($serialize);
    $var->save();
  }
}

?>