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
    $redirect_url = array('Intervention' => '@intervention?id=');

    $this->forward404Unless($request->isMethod('post'));
    $this->type = $request->getParameter('type');
    $this->id = $request->getParameter('id');
    $this->form = new CommentaireForm();
    $this->form->bind($request->getParameter('commentaire'));
    $this->commentaire = Commentaire::cleanCommentaire($request->getParameter('commentaire[commentaire]'));
    if ($request->getParameter('ok') && $this->form->isValid()) {
      $commentaire = $this->form->getObject();
      //Pas très propre mais les formulaires ne semblent pas appeler le setCommentaire...
      $commentaire->commentaire = $this->commentaire;
      $commentaire->object_type = $this->type;
      $commentaire->object_id = $this->id;
      $commentaire->is_public = 0;
      $commentaire->save();
      $this->getUser()->setFlash('notice', 'Votre commentaire a été enregistré');
      return $this->redirect($redirect_url[$this->type].$this->id);
    }
  }
}
