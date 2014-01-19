<?php

class reattachCommentaireInterventionTask extends sfBaseTask {
  protected function configure() {
    $this->namespace = 'reattach';
    $this->name = 'CommentaireIntervention';
    $this->briefDescription = 'Réattacher un commentaire à une intervention';
    $this->addArgument('cid', sfCommandArgument::REQUIRED, 'Id du commentaire à réattacher');
    $this->addArgument('iid', sfCommandArgument::REQUIRED, 'Id de l\'intervention');
    $this->addOption('env', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'test');
    $this->addOption('app', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'frontend');
 }

  protected function execute($arguments = array(), $options = array()) {
    $manager = new sfDatabaseManager($this->configuration);
    $comment = Doctrine::getTable('Commentaire')->find($arguments['cid']);
    if (!$comment) {
      print "Commentaire inexistant\n";
      return;
    }
    if ($comment->object_id != 0 || $comment->object_type != "Intervention") {
      print "Ce commentaire est déjà rattaché.";
      return;
    }
    $inter = Doctrine::getTable('Intervention')->find($arguments['iid']);
    if (!$inter) {
      print "Intervention inexistante\n";
      return;
    }
    Doctrine_Query::create()
      ->update('Commentaire')
      ->set('object_id', $inter->id)
      ->set('lien', '?', "@intervention?id=".$inter->id)
      ->where('id = ?', $comment->id)
      ->execute();
    if ($comment->is_public == 1) {
     Doctrine_Query::create()
      ->update('Intervention')
      ->set('nb_commentaires', $inter->nb_commentaires + 1)
      ->where('id = ?', $inter->id)
      ->execute();
    }
  }
}

