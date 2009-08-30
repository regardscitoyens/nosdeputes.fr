<?php

/**
 * citoyen actions.
 *
 * @package    cpc
 * @subpackage citoyen
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12479 2008-10-31 10:54:40Z fabien $
 */
class citoyenActions extends sfActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
    $this->citoyens_list = Doctrine::getTable('Citoyen')
      ->createQuery('a')
      ->execute();
    $response = $this->getResponse();
    $response->setTitle('Liste des citoyens inscrits'); 
  }
  
  public function executeShow(sfWebRequest $request)
  {
    $slug = $request->getParameter('slug');
    $this->Citoyen = Doctrine::getTable('Citoyen')->findOneBySlug($slug);
    $response = $this->getResponse();
    $response->setTitle('Mini blog de '.$this->Citoyen->login); 
  }

  public function executeNew(sfWebRequest $request)
  {
    if (!$this->getUser()->isAuthenticated()) {
      $this->form = new InscriptionForm();
      if ($request->isMethod('post'))
      {
        $values = $request->getParameter('citoyen');
        $values['pass'] = sha1($values['pass']);
        $email = $values['email'];
        
        $this->form->bind($values);
        
        if ($this->form->isValid())
        {
          $this->form->save();
          
          $this->Citoyen = Doctrine::getTable('Citoyen')->findOneByEmail($email);
          $this->connexion($this->form->getObject());
          $this->Citoyen->activation_id = md5(time()*rand());
          $this->Citoyen->save();
          $this->getUser()->setFlash('notice', 'Votre compte a ete cree avec succes');
          $slug = $this->Citoyen->slug;
          $this->redirect('@citoyen?slug='.$slug);
        }  
      }
    }
    else
    {
      $slug = $this->getUser()->getAttribute('slug');
      $this->getUser()->setFlash('notice', 'Vous etes deja inscrit');
      $this->redirect('@citoyen?slug='.$slug);
    }
  }
  
  public function executeEdit(sfWebRequest $request)
  {
    if ($this->getUser()->isAuthenticated()) {
    
      $user = Doctrine::getTable('Citoyen')->findOneById($this->getUser()->getAttribute('user_id'));
    
      $this->form = new EditUserForm($user);
      
      if ($request->isMethod('put'))
      {
        $this->form->bind($request->getParameter('sf_guard_user'));
        
        if ($this->form->isValid())
        {
          $this->form->save();
          $this->getUser()->setFlash('notice', 'Votre modification a reussi');
          $this->redirect('@citoyen?slug='.$user->slug);
        }
      }
    }
    else { $this->redirect('@signin'); }
  }
  
  public function executeActivation(sfWebRequest $request)
  {
    $activation_id = $request->getParameter('activation_id');
    
    if ($this->getUser()->isAuthenticated()) {
      
      $user = $this->getUser()->getGuardUser();
      
      if (Doctrine::getTable('Citoyen')->findOneByActivationId($activation_id))
      {
        $this->user_a_activer = Doctrine::getTable('Citoyen')->findOneByActivationId($activation_id);
        
        if ($this->user_a_activer->sf_guard_user_id == $user->id)
        {
          if (!$user->is_active)
          {
            $user->is_active = true;
            $user->addGroupByName('membre');
            $user->save();
            $this->getUser()->setFlash('notice', 'Votre compte a ete active avec succes');
            $this->redirect('@citoyen?slug='.$user->Profile->slug);
          }
          else
          {
            $this->getUser()->setFlash('notice', 'Votre compte est deja active');
            $this->redirect('@citoyen?slug='.$user->Profile->slug);
          }
        }
        else
        {
          $this->getUser()->setFlash('notice', 'Ce compte n\'est pas le votre');
          $this->redirect('@list_citoyens');
        }
      }
      else
      {
        $this->redirect('@list_citoyens');
        $this->getUser()->setFlash('notice', 'Ce compte n\'existe pas');
      }
    }
    else
    {
      $this->redirect('@sf_guard_signin');
      $this->getUser()->setFlash('notice', 'Veuillez vous identifier et cliquer a nouveau sur le lien de confirmation contenu dans l\'email');
    }
  }
  
  public function executeCirco(sfWebRequest $request)
  {
    if ($this->getUser()->isAuthenticated())
    {
      $nom_circo = $request->getParameter('nom_circo');
      $num_circo = $request->getParameter('num_circo');
      $user = $this->getUser()->getGuardUser();
      $user->Profile->nom_circo = $nom_circo;
      $user->Profile->num_circo = $num_circo;
      $user->save();
      $this->redirect('@citoyen?slug='.$user->Profile->slug);
    }
    else
    {
      $this->redirect('@sf_guard_signin');
    }
  }
  
  public function executeSignin(sfWebRequest $request)
  {
    if (!$this->getUser()->isAuthenticated()) {
    
      $this->form = new SigninForm();
      
      if ($request->isMethod('post'))
      {
        $this->form->bind($request->getParameter('signin'));
        
        if ($this->form->isValid())
        {
          $user = Doctrine::getTable('Citoyen')->findOneByLogin($this->form->getValue('login'));
          if (sha1($this->form->getValue('pass')) == $user->pass)
          {
            $this->connexion($user);
            $this->redirect('@homepage');
          }
        }
      }
    }
    else
    {
      $slug = $this->getUser()->getAttribute('slug');
      $this->getUser()->setFlash('notice', 'Vous etes deja connecte');
      $this->redirect('@citoyen?slug='.$slug);
    }
  }
  
  public function connexion($user)
  {
    // signin
    $this->getUser()->setAttribute('user_id', $user->getId());
    $this->getUser()->setAttribute('login', $user->getLogin());
    $this->getUser()->setAttribute('slug', $user->getSlug());
    $this->getUser()->setAuthenticated(true);
    $this->getUser()->clearCredentials();
    $this->getUser()->addCredentials($user->getRole());

    // save last login
    $user->setLastLogin(date('Y-m-d H:i:s'));
    $user->save();

    // remember?
    
    
  }
  
  public function executeSignout()
  {
    $this->getUser()->getAttributeHolder()->clear();
    $this->getUser()->clearCredentials();
    $this->getUser()->setAuthenticated(false);
    $this->getUser()->setFlash('notice', 'Vous etes deconnecte');
    $this->redirect('@homepage');
  }
  
  public function executeDelete()
  {
    if ($this->getUser()->isAuthenticated())
    {
      $user = $this->getUser()->getGuardUser();
      $user->delete();
      $this->getUser()->signOut();
      $this->getUser()->setFlash('notice', 'Votre compte a ete supprime avec succes');
    }
    else
    {
      $this->redirect('@sf_guard_signin');
    }
  }
}