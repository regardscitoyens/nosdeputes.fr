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
      ->andWhere('c.is_public = ?', 1)
      ->orderBy('c.created_at DESC');
  }
  public function executePost(sfWebRequest $request)
  {
    $redirect_url = array('Intervention' => '@intervention?id=', 'Amendement' => '@amendement?id=', 'QuestionEcrite' => '@question?id=');
    $about = array('Intervention' => "Suite aux propos d", 'Amendement' => "Au sujet d'un amendement déposé", 'QuestionEcrite' => "A propos d'une question écrite d");

    $this->type = $request->getParameter('type');
    $this->id = $request->getParameter('id');

    $this->commentaire = myTools::clearHtml($request->getParameter('commentaire[commentaire]'));
    $this->unique_form = $request->getParameter('unique_form');

    if ($this->getUser()->getAttribute('commentaire_'.$this->type.'_'.$this->id) != $this->unique_form) {
      $this->getUser()->setFlash('error', 'Vous avez déjà posté ce commentaire...');
      return $this->redirect($redirect_url[$this->type].$this->id);
    }
    if (!$request->isMethod('post')) {
      $this->getUser()->setFlash('error', 'Un problème technique est survenu');
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
      $this->getUser()->setAttribute('partial', 'inscriptioncom');
      if (!($citoyen_id = myUser::CreateAccount($values['nom'], $values['email'], $this)))
	return ;
      $is_active = false;
    } else { //Si pas de (login et mdp) ou (email, login)
      $this->getUser()->setFlash('error', 'Vous devez avoir un compte et y être connecté pour poster un commentaire.<br />Le formulaire ci-dessous vous permet de vous identifier ou de vous inscrire sur le site.');
      return;
    }
    $ip = '';
    if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
	    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    $ip = $ip . ',';
    if(isset($_SERVER['HTTP_CLIENT_IP'])) 
	    $ip = $ip . $_SERVER['HTTP_CLIENT_IP'];
    $ip = $ip . ',';
    if(isset($_SERVER['REMOTE_ADDR'])) 
	    $ip = $ip . $_SERVER['REMOTE_ADDR']; 
  
    $commentaire = $this->form->getObject();
    //Pas trouvé d'autre moyen que de bypasser le form pour conserver la presentation htmlisée
    $commentaire->commentaire = $this->commentaire;
    $commentaire->object_type = $this->type;
    $commentaire->object_id = $this->id;
    $commentaire->lien = $redirect_url[$this->type].$this->id;
    $object = doctrine::getTable($this->type)->find($this->id);
    $present = '';
    if ($this->type != 'QuestionEcrite') {
      if ($section = $object->getSection())
        $present = $section->getSection(1)->getTitre();
      if ($present == '' && $this->type == 'Intervention' && $object->type == 'commission')
        $present = $object->getSeance()->getOrganisme()->getNom();    
    }
    if ($present != '') $present .= ' - ';   
    else $present = '';
    $present .= $about[$this->type];
    if (isset($object->parlementaire_id)) {
      $nom = $object->getParlementaire()->nom;
      if (preg_match('/^[AEIOUYÉÈÊ]/', $nom)) $nom = '\''.$nom;
      else $nom = 'e '.$nom;
      $present .= $nom;
    }
    $present .= ' le '.date('d/m/Y', strtotime($object->date));
    $commentaire->presentation = $present;
    $commentaire->citoyen_id = $citoyen_id;
    $commentaire->is_public = $is_active;
    $commentaire->ip_address = $ip;
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
    $query = Doctrine::getTable('Commentaire')
      ->createQuery('c')
      ->leftJoin('c.CommentaireParlementaires cp')
      ->andWhere('c.is_public = ?', 1)
      ->orderBy('created_at DESC')->limit(10);
    if (($slug = $request->getParameter('slug'))) { 
      $this->parlementaire = Doctrine::getTable('Parlementaire')->findOneBySlug($slug);
      $this->forward404Unless($this->parlementaire);
      $query->andWhere('cp.parlementaire_id = ?', $this->parlementaire->id);
    }
    $this->commentaires = $query->execute();
    $this->feed = new sfRssFeed();
  }
  public function executeSeance(sfWebRequest $request)
  {
    $this->setLayout(false);
    $seance_id = $request->getParameter('seance');
    $this->forward404Unless($seance_id);
    $this->commentaires = Doctrine::getTable('Intervention')->createQuery('i')->select('i.id, i.nb_commentaires')->where('seance_id = ?', $seance_id)->fetchArray();
    $this->getResponse()->setHttpHeader('content-type', 'text/plain');
  }
  public function executeShowSeance(sfWebRequest $request)
  {
    $this->setLayout(false);
    $this->id = $request->getParameter('id');
    $this->forward404Unless($this->id);
    $this->comments = Doctrine::getTable('Commentaire')->createQuery('c')
      ->where('object_id = ?', $this->id)
      ->andWhere('object_type = ?', 'Intervention')
      ->andWhere('is_public = ?', 1)
      ->orderBy('created_at DESC')
      ->limit(3)
      ->execute();    
    $this->getResponse()->setHttpHeader('content-type', 'text/plain');
  }

  public function executeList(sfWebRequest $request)
  {
    $request->setParameter('rss', array(array('link' => '@commentaires_rss', 'title'=>'Les derniers commentaires en RSS')));

    $this->comments = Doctrine::getTable('Commentaire')->createQuery('c')
      ->andWhere('is_public = ?', 1)
      ->orderBy('created_at DESC');
    $this->response->setTitle('Tous les commentaires citoyens');
  }
}
