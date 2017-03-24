<?php

class resetTitresCommentsTask extends sfBaseTask {
  protected function configure() {
    $this->namespace = 'reset';
    $this->name = 'TitresComments';
    $this->briefDescription = 'Set Amendements Tombés pour lois zappées';
    $this->addOption('env', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'test');
    $this->addOption('app', null, sfCommandOption::PARAMETER_OPTIONAL, 'Changes the environment this task is run in', 'frontend');
 }

  protected function execute($arguments = array(), $options = array()) {
    $manager = new sfDatabaseManager($this->configuration);
    $about = array('Intervention' => "Suite aux propos d", 'Amendement' => "Au sujet d'un amendement déposé", 'QuestionEcrite' => "A propos d'une question écrite d");
    $comments = doctrine::getTable('Commentaire')->findAll();
    foreach($comments as $comment) {
      $object = doctrine::getTable($comment->object_type)->find($comment->object_id);
      $present = '';
      if ($comment->object_type != 'QuestionEcrite') {
        if ($section = $object->getSection())
          $present = $section->getSection(1)->getTitre();
        if ($present == '' && $comment->object_type == 'Intervention' && $object->type == 'commission')
          $present = $object->getSeance()->getOrganisme()->getNom();
      }
      if ($present != '') $present .= ' - ';
      else $present = '';
      $present .= $about[$comment->object_type];
      if (isset($object->parlementaire_id)) {
        $nom = $object->getParlementaire()->nom;
        if (preg_match('/^[AEIOUYÉÈÊ]/', $nom)) $nom = '\''.$nom;
        else $nom = 'e '.$nom;
        $present .= $nom;
      }
      $present .= ' le '.date('d/m/Y', strtotime($object->date));
      $comment->presentation = $present;

      $comment->save();
      $comment->free();
    }
  }
}

