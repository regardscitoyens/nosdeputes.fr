<?php

class commentaireComponents extends sfComponents
{
  public function executeForm() {
    /* Respect du hack de l'action */
    $_GET['isAuthenticated'] = 1;
    if (!isset($this->form))
      $this->form = new CommentaireForm();
    
    if (isset($this->object)) {
      $this->id = $this->object->id;
      $this->type = get_class($this->object);
    }
    $this->unique_form = md5(rand());
    if (!isset($this->follow_talk))
      $this->follow_talk = 0;
    $this->getUser()->setAttribute('commentaire_'.$this->type.'_'.$this->id, $this->unique_form);
  }

  public function executeShowAll() {
    $id = $this->object->id;
    $type = get_class($this->object);
    $query = Doctrine::getTable('Commentaire')
      ->createQuery('c');
    if ($type === 'ArticleLoi' || $type === 'Titreloi')
      $query->leftJoin('c.Objects co')
        ->where('(c.object_type = ? AND c.object_id = ?) OR (co.object_type = "'.$type.'" AND co.object_id = ?)', array($type, $id, $id));
    else $query->where('(c.object_type = ? AND c.object_id = ?)', array($type, $id));
    if ($citid = $this->getUser()->getAttribute('user_id'))
      $query->andWhere('is_public = 1 OR citoyen_id = ?', $citid);
    else $query->andWhere('is_public = ?', 1);
    $query->orderBy('created_at');
    $this->commentaires = $query->execute();
  }
  
  public function executeShowAllCitoyen() {
    $id = $this->id;
    $query = Doctrine::getTable('Commentaire')
      ->createQuery()
      ->where('citoyen_id = ? ', $id);
    if ($id != $this->getUser()->getAttribute('user_id'))
      $query->andWhere('is_public = ?', 1);
    $query->orderBy('created_at DESC');
    $this->commentaires = $query->execute();
  }
 
  public function executeLastObject() {
    $id = $this->object->id;
    $type = get_class($this->object);
    $query = Doctrine::getTable('Commentaire')->createQuery('c')
      ->leftJoin('c.Objects co')
      ->where('co.object_type = ?', $type)
      ->andWhere('co.object_id = ?', $id)
      ->andWhere('is_public = 1')
      ->orderBy('c.created_at DESC')
      ->limit(4);
    $this->commentaires = $query->execute();
  }

  public function executePager() {
    if (!$this->query_commentaires)
      throw new Exception('query_commentaires parameter missing');

    $pager = new sfDoctrinePager('Commentaire',20);
    $pager->setQuery($this->query_commentaires);
    $pager->setPage($this->request->getParameter('page', 1));
    $pager->init();

    $this->pager = $pager;
  }
  
  public function executeShowWidget() {
    $query = Doctrine::getTable('Commentaire')->createQuery('c')
      ->where('c.id = (SELECT c2.id FROM commentaire c2 WHERE c.citoyen_id = c2.citoyen_id ORDER BY c2.created_at DESC LIMIT 1)')
      ->andWhere('c.is_public = 1')
      ->orderBy('c.created_at DESC')
      ->limit(5);
    $this->commentaires = $query->execute();
  }
}
