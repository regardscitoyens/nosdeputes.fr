<?php

class setObjectsGroupeAcronymeTask extends sfBaseTask {

  protected function configure() {
    $this->namespace = 'set';
    $this->name = 'ObjectsGroupeAcronyme';
    $this->briefDescription = 'Complete missing groupe_acronyme fields on objects coming from old 2012 dump';
    $this->addArgument('class', sfCommandArgument::REQUIRED, 'Classe de l\'objet');
    $this->addOption('env', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'test');
    $this->addOption('app', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'frontend');
  }

  protected function execute($arguments = array(), $options = array()) {
    $manager = new sfDatabaseManager($this->configuration);
    $class = $arguments['class'];
    $valid_classes = array("ParlementaireOrganisme", "Presence", "Intervention", "Amendement", "QuestionEcrite", "ParlementaireTexteloi", "ParlementaireAmendement");
    if ($class == 'all') {
      foreach ($valid_classes as $class)
        self::fixClass($class);
    } else if (!in_array($class, $valid_classes)) {
      echo "ERREUR : $class n'est pas une classe avec groupe_acronyme\n";
      return;
    } else self::fixClass($class);
  }

  protected static function fixClass($class) {
    echo "Complete ".$class."s:\n";
    $ct = 0;
    $field = ($class == "Amendement" ? 'auteur' : 'parlementaire');
    $done = false;
    while (!$done) {
      $objects = Doctrine::getTable($class)
        ->createQuery()
        ->where($field."_groupe_acronyme IS NULL")
        ->andWhere($field."_id IS NOT NULL")
        ->limit(1000)
        ->execute();
      if (!count($objects))
        $done = true;
      else foreach ($objects as $obj) {
        $obj->getGroupeAcronyme();
        $ct++;
      }
      print " - $class $ct\n";
    }
    echo "  => fixed $ct objects\n\n";
  }
}

