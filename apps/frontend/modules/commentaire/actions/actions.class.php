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

    $this->commentaire = myTools::clearHtml($request->getParameter('commentaire[commentaire]'));
    $this->unique_form = $request->getParameter('unique_form');

    if ($this->getUser()->getAttribute('commentaire_'.$this->type.'_'.$this->id) != $this->unique_form) {
      $this->getUser()->setFlash('error', 'Vous avez déjà posté ce commentaire...');
      return $this->redirect($redirect_url[$this->type].$this->id);
    }
    $this->form = new CommentaireForm();
		
    $values = $request->getParameter('commentaire');
    $this->form->bind($values);
		
    if (!$request->getParameter('ok') || !$this->form->isValid())
      return ;
    if ($this->getUser()->isAuthenticated()) {
      $citoyen_id = $this->getUser()->getAttribute('user_id');
      if ($this->getUser()->getAttribute('is_active') == true) { $is_active = true; }
      else { $is_active = false; }
    }
    else if ($values['nom'] && $values['email']) {
      if (Doctrine::getTable('Citoyen')->findOneByLogin($values['nom'])) {
        $this->getUser()->setFlash('error', 'Ce nom d\'utilisateur existe déjà.');
        return;
      }

      if (Doctrine::getTable('Citoyen')->findOneByEmail($values['email'])) {
        $this->getUser()->setFlash('error', 'Cette adresse email existe déjà.');
        return;
      }

      $citoyen = new Citoyen;
      $citoyen->login = $values['nom'];
      $citoyen->email = $values['email'];
      $citoyen->activation_id = md5(time()*rand());
      $citoyen->save();
      $citoyen_id = $citoyen->getId();
      $is_active = false;
      $this->getComponent('citoyen', 'connexion', array('login' => $citoyen->login));
      $this->getComponent('mail', 'send', array(
            'subject'=>'Inscription NosDéputés.fr', 
            'to'=>array($citoyen->email), 
            'partial'=>'inscriptioncom', 
            'mailContext'=>array('activation_id' => $citoyen->activation_id) 
            ));
    }
    else if ($values['login'] && $values['password']) {
      /* Tangui : Il y a moyen de refactoriser cette partie dans le modèle. */
      if (! Doctrine::getTable('Citoyen')->findOneByLogin($values['login'])) {
        sleep(3);
        $this->getUser()->setFlash('error', 'Utilisateur ou mot de passe incorrect');
        return;
      }
      $user = Doctrine::getTable('Citoyen')->findOneByLogin($values['login']);
      if (sha1($values['password']) != $user->password) {
        sleep(3);
        $this->getUser()->setFlash('error', 'Utilisateur ou mot de passe incorrect');
        return;
      }
      $this->getComponent('citoyen', 'connexion', array('login' => $user->login));
      $citoyen_id = $user->id;
      $is_active = true;
    }
    else { //Si pas de (login et mdp) ou (email, login)
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
    
    if (!$is_active) {
      $pas_confirme_mail = ', pour le rendre public, cliquez sur le lien d\'activation contenu dans l\'email que nous vous avons envoyé afin de terminer votre inscription.';
    }

    if (isset($object->parlementaire_id)) {
      $commentaire->addParlementaire($object->parlementaire_id);
    }else{
      $object->Parlementaires;
    }
    if (isset($object->Parlementaires)) {
      foreach($object->Parlementaires as $p)
      $commentaire->addParlementaire($p->id);
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
}
