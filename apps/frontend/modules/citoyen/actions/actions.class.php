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
    $this->citoyens_list = Doctrine::getTable('sfGuardUserProfile')
      ->createQuery('a')
      ->execute();
    $response = $this->getResponse();
    $response->setTitle('Liste des citoyens inscrits'); 
  }
  
  public function executeShow(sfWebRequest $request)
  {
    $slug = $request->getParameter('slug');
    $this->Citoyen = Doctrine::getTable('sfGuardUserProfile')->findOneBySlug($slug);
    $response = $this->getResponse();
    $response->setTitle('Mini blog de '.$this->Citoyen->username); 
  }
  
  public function executeNew(sfWebRequest $request)
  {
    if (!$this->getUser()->isAuthenticated()) {
      $this->form = new InscriptionForm();
      if ($request->isMethod('post'))
      {
        $activation_id = md5(time()*rand());
        
        $values = $request->getParameter('sf_guard_user');
        $values['Profile']['username'] = $values['username'];
        
        $this->form->bind($values);
        
        if ($this->form->isValid())
        {
          $this->form->save();
          
          $this->getUser()->signIn($this->form->getObject());
          $user = $this->getUser()->getGuardUser();
          $user->Profile->activation_id = $activation_id;
          $user->is_active = false;
          $user->save();
          $slug = $user->Profile->slug;
          
          $this->getUser()->setFlash('notice', 'Votre compte a ete cree avec succes');
          $this->redirect('@citoyen?slug='.$slug);
        }  
      }
    }
    else
    {
      $user = $this->getUser()->getGuardUser();
      $this->getUser()->setFlash('notice', 'Vous etes deja inscrit');
      $this->redirect('@citoyen?slug='.$user->Profile->slug);
    }
  }
  
  public function executeEdit(sfWebRequest $request)
  {
    if ($this->getUser()->isAuthenticated()) {
    $user = $this->getUser()->getGuardUser();
    
    $this->form = new EditUserForm($user);
      
      if ($request->isMethod('put'))
      {
        $this->form->bind($request->getParameter('sf_guard_user'));
        
        if ($this->form->isValid())
        {
          $this->form->save();
          $this->getUser()->setFlash('notice', 'Votre modification a reussi');
          $this->redirect('@citoyen?slug='.$user->Profile->slug);
        }  
      }
    }
    else { $this->redirect('@list_citoyens'); } 
  }
  
  public function executeActivation(sfWebRequest $request)
  {
    $activation_id = $request->getParameter('activation_id');
    
    if ($this->getUser()->isAuthenticated()) {
      
      $user = $this->getUser()->getGuardUser();
      
      if (Doctrine::getTable('sfGuardUserProfile')->findOneByActivationId($activation_id))
      {
        $this->user_a_activer = Doctrine::getTable('sfGuardUserProfile')->findOneByActivationId($activation_id);
        
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