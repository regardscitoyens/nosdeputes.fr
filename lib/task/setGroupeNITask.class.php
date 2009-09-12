<?php

class SetGroupeNITask extends sfBaseTask
{
  protected function configure()
  {
    $this->namespace = 'set';
    $this->name = 'NI';
    $this->briefDescription = 'Corrige erreur NI';
  }

  protected function execute($arguments = array(), $options = array())
  {
    $manager = new sfDatabaseManager($this->configuration);
    $parls = doctrine::getTable('Parlementaire')->createQuery('p')
        ->where('fin_mandat is null')
        ->andWhere('groupe_acronyme is null')
        ->execute();
    foreach ($parls as $parl) {
      $NI = array(explode(' / ', "députés non-inscrits / non-rattaché"));
      $parl->setGroupe($NI);
      $parl->save();
    }
  }
}
