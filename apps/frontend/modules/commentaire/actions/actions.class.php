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

    $this->type = $request->getParameter('type');
    $this->id = $request->getParameter('id');

    $this->commentaire = myTools::clearHtml($request->getParameter('commentaire[commentaire]'));
    $this->unique_form = $request->getParameter('unique_form');

    if ($this->getUser()->getAttribute('commentaire_'.$this->type.'_'.$this->id) != $this->unique_form) {
      $this->getUser()->setFlash('error', 'Vous avez déjà posté ce commentaire...');
      return $this->redirect($redirect_url[$this->type].$this->id);
    }
    if (!$request->isMethod('post')) {
      $this->getUser()->setFlash('error', 'Oups erreur de routage');
      return $this->redirect($redirect_url[$this->type].$this->id);
    }
		
    $values = $request->getParameter('commentaire');

    /** On logue l'utilisateur si il a donné un login et mot de passe correct sinon creation du form et template**/
    $isAuthenticated = $this->getUser()->isAuthenticated();
    /** Pas loggué on s'assure que quelqu'un n'a pas trouvé notre hack */
    $_GET['isAuthenticated'] = $isAuthenticated;
    if ($request->getParameter('commentaire[login]') && $request->getParameter('commentaire[password]')) {
      if (!($citoyen_id = myUser::SignIn($values['login'],
$values['password'], false, $this))) {
	$this->form = new CommentaireForm();
	$this->form->bind($values);
	return ;
      }
      $is_active = true;
      $isAuthenticated = 1;
      //Pour communication avec CommentaireForm->configure()
      $_GET['isAuthenticated'] = 1;
      //Invalide les champs dont on a plus besoin come l'utilisateur est connecté
      unset($values['login']);
      unset($values['password']);
      unset($values['email']);
      unset($values['nom']);
    }

    /** Creation du form et validation */
    $this->form = new CommentaireForm();
    $this->form->bind($values);	
    if (!$request->getParameter('ok') || !$this->form->isValid())
      return ;


    if ($isAuthenticated) {
      $citoyen_id = $this->getUser()->getAttribute('user_id');
      $is_active = $this->getUser()->getAttribute('is_active');
    } else if ($values['nom'] && $values['email']) {
      if (!($citoyen_id = myUser::CreateAccount($values['nom'], $values['email'], $this)))
	return ;
      $is_active = false;
    } else { //Si pas de (login et mdp) ou (email, login)
      $this->getUser()->setFlash('error', 'Vous devez avoir un compte et y être connecté pour poster un commentaire.<br />Le formulaire ci-dessous vous permet de vous identifier ou de vous inscrire sur le site.');
      return;
    }
      
    $commentaire = $this->form->getObject();
    //Pas trouvé d'autre moyen que de bypasser le form pour conserver la presentation htmlisée
    $commentaire->commentaire = $this->commentaire;
    $commentaire->object_type = $this->type;
    $commentaire->object_id = $this->id;
    $commentaire->lien = $redirect_url[$this->type].$this->id;
    $object = doctrine::getTable($this->type)->find($this->id);
    $commentaire->presentation = $about[$this->type].date('d/m/Y', time($object->date));
    $commentaire->citoyen_id = $citoyen_id;
    $commentaire->is_public = $is_active;
    $commentaire->save();

    $object->updateNbCommentaires();
    $object->save();

    if (isset($object->parlementaire_id)) {
      $commentaire->addParlementaire($object->parlementaire_id);
    }else{
      $object->Parlementaires;
    }
    if (isset($object->Parlementaires)) {
      foreach($object->Parlementaires as $p)
      $commentaire->addParlementaire($p->id);
    }
      
    $pas_confirme_mail = '';
    if (!$is_active) {
      $pas_confirme_mail = ', pour le rendre public, cliquez sur le lien d\'activation contenu dans l\'email que nous vous avons envoyé afin de terminer votre inscription.';
    }
    $this->getUser()->setFlash('notice', 'Votre commentaire a été enregistré'.$pas_confirme_mail);
    $this->getUser()->getAttributeHolder()->remove('commentaire_'.$this->type.'_'.$this->id);

    return $this->redirect($commentaire->lien);
  }

  public function executeRss(sfWebRequest $request) 
  {
    $this->parlementaire = Doctrine::getTable('Parlementaire')->findOneBySlug($request->getParameter('slug'));
    $this->forward404Unless($this->parlementaire);
    $this->commentaires = Doctrine::getTable('Commentaire')->createQuery('c')->leftJoin('c.CommentaireParlementaires cp')->where('cp.parlementaire_id = ?', $this->parlementaire->id)->orderBy('created_at DESC')->limit(10)->execute();
    $this->feed = new sfRssFeed();
  }
  public function executeSeance(sfWebRequest $request)
  {
    $this->setLayout(false);
    $seance_id = $request->getParameter('seance');
    $this->forward404Unless($seance_id);
    $this->commentaires = Doctrine::getTable('Intervention')->createQuery('i')->select('i.id, i.nb_commentaires')->where('seance_id = ?', $seance_id)->fetchArray();
  }
  public function executeShowSeance(sfWebRequest $request)
  {
    $this->setLayout(false);
    $this->id = $request->getParameter('id');
    $this->forward404Unless($this->id);
    $this->comments = Doctrine::getTable('Commentaire')->createQuery('c')
      ->where('object_id = ?', $this->id)
      ->andWhere('object_type = ?', 'Intervention')
      ->andWhere('is_public = 1')
      ->orderBy('updated_at DESC')
      ->limit(3)
      ->execute();    
  }
}
