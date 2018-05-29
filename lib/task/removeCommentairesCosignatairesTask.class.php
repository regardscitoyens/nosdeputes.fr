<?php

class removeCommentairesCosignatairesTask extends sfBaseTask {
  protected function configure() {
    $this->namespace = 'remove';
    $this->name = 'CommentairesCosignataires';
    $this->briefDescription = 'Removes association between comments and cosginataires of amendements';
    $this->addOption('env', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'test');
    $this->addOption('app', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'frontend');
 }

  protected function execute($arguments = array(), $options = array()) {
    $manager = new sfDatabaseManager($this->configuration);
    foreach (Doctrine::getTable('Commentaire')->findByObjectType('Amendement') as $comment) {
      $amd = Doctrine::getTable('Amendement')->find($comment->object_id);
      foreach($comment->getParlementaires() as $parl)
        if ($parl->id != $amd->auteur_id) {
          $comment->rmObject("Parlementaire", $parl->id);
          echo "removed comment ".$comment->id." from ".$parl->slug."\n";
        }
    }
  }
}
