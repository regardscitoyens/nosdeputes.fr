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
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executePost(sfWebRequest $request)
  {
    $redirect_url = array('Intervention' => '@intervention?id=', 'Amendement' => '@amendement?id=', 'QuestionEcrite' => '@question?id=');


    $this->forward404Unless($request->isMethod('post'));
    $this->type = $request->getParameter('type');
    $this->id = $request->getParameter('id');

    $this->form = new CommentaireForm();
    $this->form->bind($request->getParameter('commentaire'));
    $this->commentaire = Commentaire::cleanCommentaire($request->getParameter('commentaire[commentaire]'));
    $this->unique_form = $request->getParameter('unique_form');

    if ($request->getParameter('ok') && $this->form->isValid()) {
      if ($this->getUser()->getAttribute('commentaire_'.$this->type.'_'.$this->id)
	  != $this->unique_form) {

	$this->getUser()->setFlash('notice', 'Vous avez déjà posté ce commentaire...');
	return $this->redirect($redirect_url[$this->type].$this->id);
      }

      $commentaire = $this->form->getObject();
      //Pas très propre mais les formulaires ne semblent pas appeler le setCommentaire...
      $commentaire->commentaire = $this->commentaire;
      $commentaire->object_type = $this->type;
      $commentaire->object_id = $this->id;
      $commentaire->is_public = 0;
      $commentaire->save();
      $object = doctrine::getTable($this->type)->find($this->id);
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
      return $this->redirect($redirect_url[$this->type].$this->id);
    }
  }
}
