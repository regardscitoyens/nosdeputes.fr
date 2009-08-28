<?php

class commentaireComponents extends sfComponents
{
  public function executeForm() {
    if (!isset($this->form))
      $this->form = new CommentaireForm();
    
    if (isset($this->object)) {
      $this->id = $this->object->id;
      $this->type = get_class($this->object);
    }
    $this->unique_form = md5(rand());
    $this->getUser()->setAttribute('commentaire_'.$this->type.'_'.$this->id, $this->unique_form);
  }

  public function executeShow() {
    $id = $this->object->id;
    $type = get_class($this->object);
    $this->commentaires = Doctrine::getTable('Commentaire')->createQuery()
      ->where('(object_type = ? AND object_id = ?)', array($type, $id))->execute();
  }
}