<?php

/**
 * commentaire actions.
 *
 * @package    cpc
 * @subpackage commentaire
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12479 2008-10-31 10:54:40Z fabien $
 */
class commentaireActions extends sfActions
{
  public function executeParlementaire(sfWebRequest $request) 
  {
    $this->parlementaire = Doctrine::getTable('Parlementaire')->findOneBySlug($request->getParameter('slug'));
    $this->q_commentaires = Doctrine::getTable('Commentaire')->createQuery('c')
      ->leftJoin('c.CommentaireParlementaires cp')
      ->where('cp.parlementaire_id = ?', $this->parlementaire->id)
      ->orderBy('c.created_at DESC');
  }
  public function executePost(sfWebRequest $request)
  {
    $redirect_url = array('Intervention' => '@intervention?id=', 'Amendement' => '@amendement?id=', 'QuestionEcrite' => '@question?id=');
    $about = array('Intervention' => "A propos d'une intervention du ", 'Amendement' => "A propos d'un amendement déposé le ", 'QuestionEcrite' => "A propos d'une question ecrite du ");

    $this->forward404Unless($request->isMethod('post'));
    $this->type = $request->getParameter('type');
    $this->id = $request->getParameter('id');

    $this->form = new CommentaireForm();
    $this->form->bind($request->getParameter('commentaire'));
    $this->commentaire = myTools::clearHtml($this->form->getValue('commentaire'));
    $this->unique_form = $request->getParameter('unique_form');

    if ($this->getUser()->getAttribute('commentaire_'.$this->type.'_'.$this->id)
	!= $this->unique_form) {
      $this->getUser()->setFlash('error', 'Vous avez déjà posté ce commentaire...');
      return $this->redirect($redirect_url[$this->type].$this->id);
    }

    if ($request->getParameter('ok') && $this->form->isValid()) {

      $commentaire = $this->form->getObject();
      //Pas très propre mais les formulaires ne semblent pas appeler le setCommentaire...
      $commentaire->commentaire = $this->commentaire;
      $commentaire->object_type = $this->type;
      $commentaire->object_id = $this->id;
      $commentaire->is_public = 0;
      $commentaire->lien = $redirect_url[$this->type].$this->id;
      $object = doctrine::getTable($this->type)->find($this->id);
      $commentaire->presentation = $about[$this->type].date('d/m/Y', time($object->date));
      $commentaire->save();

      if (isset($object->parlementaire_id)) {
	$commentaire->addParlementaire($object->parlementaire_id);
      }else
	$object->Parlementaires;
	if (isset($object->Parlementaires)) {
	  foreach($object->Parlementaires as $p)
	    $commentaire->addParlementaire($p->id);
	}

      $this->getUser()->setFlash('notice', 'Votre commentaire a été enregistré');
      $this->getUser()->getAttributeHolder()->remove('commentaire_'.$this->type.'_'.$this->id);
      return $this->redirect($commentaire->lien);
    }
  }
  public function executeRss(sfWebRequest $request) 
  {
    $this->parlementaire = Doctrine::getTable('Parlementaire')->findOneBySlug($request->getParameter('slug'));
    $this->forward404Unless($this->parlementaire);
    $this->commentaires = Doctrine::getTable('Commentaire')->createQuery('c')->leftJoin('c.CommentaireParlementaires cp')->where('cp.parlementaire_id = ?', $this->parlementaire->id)->orderBy('created_at DESC')->limit(10)->execute();
    $this->feed = new sfRssFeed();
  }
}
