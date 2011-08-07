<?php

class correctTagLoisTask extends sfBaseTask {
  protected function configure() {
    $this->namespace = 'correct';
    $this->name = 'TagLois';
    $this->briefDescription = 'Corrige le tagging pour un numéro de loi mal enregistré de la forme xxxxyyyy';
    $this->addArgument('loi_1', sfCommandArgument::REQUIRED, 'Premier bon numéro');
    $this->addArgument('loi_2', sfCommandArgument::REQUIRED, 'Second bon numéro');
    $this->addOption('env', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'test');
    $this->addOption('app', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'frontend');
 }

  protected function execute($arguments = array(), $options = array()) {
    $manager = new sfDatabaseManager($this->configuration);
    foreach(array($arguments['loi_1'].$arguments['loi_2'],$arguments['loi_2'].$arguments['loi_1']) as $wrong) {
      $oldtags = Doctrine::getTable('Tag')->findByTripleValue($wrong);
      if ($oldtags) foreach($oldtags as $oldtag) {
        $taggings = Doctrine::getTable('Tagging')->findByTagId($oldtag->id);
        if ($taggings) foreach($taggings as $tagging) {
          $object = Doctrine::getTable($tagging->taggable_model)->find($tagging->taggable_id);
          $object->addTag('loi:numero='.$arguments['loi_1']);
          $object->addTag('loi:numero='.$arguments['loi_2']);
          $object->save();
          $object->free();
          $tagging->free();
        }
        Doctrine_Query::create()->delete('Tagging t')->where('t.tag_id = ?', $oldtag->id)->execute();
        $oldtag->free();
      }
      Doctrine_Query::create()->delete('Tag t')->where('t.triple_value = ?', $wrong)->execute();
    }
  }
}

