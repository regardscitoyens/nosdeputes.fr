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
          $this->getUser()->setFlash('notice', 'Votre compte a été crée avec succès');
          $slug = $this->Citoyen->slug;
          $this->redirect('@citoyen?slug='.$slug);
        }  
      }
    }
    else
    {
      $slug = $this->getUser()->getAttribute('slug');
      $this->getUser()->setFlash('notice', 'Vous êtes déja inscrit');
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
          $this->getUser()->setFlash('notice', 'Votre modification a réussi');
          $this->redirect('@citoyen?slug='.$user->slug);
        }
      }
    }
    else { $this->redirect('@signin'); }
  }
  
  public function executeActivation(sfWebRequest $request)
  {
    $activation_id = $request->getParameter('activation_id');
    
    if ($this->getUser()->isAuthenticated()) 
    {
      if (Doctrine::getTable('Citoyen')->findOneByActivationId($activation_id))
      {
        $user = Doctrine::getTable('Citoyen')->findOneByActivationId($activation_id);
        
        if ($user->id == $this->getUser()->getAttribute('user_id'))
        {
          if (!$user->is_active)
          {
            $user->is_active = true;
            $user->save();
            $this->getUser()->setFlash('notice', 'Votre compte a été activé avec succès');
            $this->redirect('@citoyen?slug='.$user->slug);
          }
          else
          {
            $this->getUser()->setFlash('error', 'Votre compte est deja activé');
            $this->redirect('@citoyen?slug='.$user->slug);
          }
        }
        else
        {
          $this->getUser()->setFlash('error', 'Ce compte n\'est pas le votre');
          $this->redirect('@list_citoyens');
        }
      }
      else
      {
        $this->redirect('@list_citoyens');
        $this->getUser()->setFlash('error', 'Ce compte n\'existe pas');
      }
    }
    else
    {
      $this->redirect('@signin');
      $this->getUser()->setFlash('error', 'Veuillez vous identifier et cliquer a nouveau sur le lien de confirmation contenu dans l\'email');
    }
  }
  
  public function executeAddcirco(sfWebRequest $request)
  {
    if ($this->getUser()->isAuthenticated())
    {
      $nom_circo = $request->getParameter('nom_circo');
      $num_circo = $request->getParameter('num_circo');
      $user = Doctrine::getTable('Citoyen')->findOneById($this->getUser()->getAttribute('user_id'));
      $user->nom_circo = $nom_circo;
      $user->num_circo = $num_circo;
      $user->save();
      $this->redirect('@citoyen?slug='.$user->slug);
    }
    else
    {
      $this->redirect('@signin');
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
          if (Doctrine::getTable('Citoyen')->findOneByLogin($this->form->getValue('login')))
          {
            $user = Doctrine::getTable('Citoyen')->findOneByLogin($this->form->getValue('login'));
            if (sha1($this->form->getValue('pass')) == $user->pass)
            {
              $this->connexion($user);
              $this->redirect('@citoyen?slug='.$user->slug);
            }
            else
            {
              sleep(3);
              $this->getUser()->setFlash('error', 'Le nom d\'utilisateur et le mot de passe ne correspondent pas.');
            }
          }
          else
          {
            sleep(3);
            $this->getUser()->setFlash('error', 'Ce nom d\'utilisateur n\'existe pas.');
          }
        }
      }
    }
    else
    {
      $slug = $this->getUser()->getAttribute('slug');
      $this->getUser()->setFlash('notice', 'Vous êtes déja connecté');
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
    $this->deconnexion();
    $this->getUser()->setFlash('notice', 'Vous avez été déconnecté avec succès');
    $this->redirect('@homepage');
  }
  
  protected function deconnexion()
  {
    $this->getUser()->getAttributeHolder()->clear();
    $this->getUser()->clearCredentials();
    $this->getUser()->setAuthenticated(false);
  }
  
  public function executeDelete()
  {
    if ($this->getUser()->isAuthenticated())
    {
      $user = Doctrine::getTable('Citoyen')->findOneById($this->getUser()->getAttribute('user_id'));
      $user->delete();
      $this->deconnexion();
      $this->getUser()->setFlash('notice', 'Votre compte a ete supprimé avec succès');
      $this->redirect('@homepage');
    }
    else
    {
      $this->getUser()->setFlash('error', 'Vous devez être connecté pour exécuter cette action');
      $this->redirect('@signin');
    }
  }
}