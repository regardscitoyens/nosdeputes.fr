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
      ->createQuery('c')
      ->where('c.is_active = ?', true)
      ->execute();
    $response = $this->getResponse();
    $response->setTitle('Liste des citoyens inscrits'); 
  }
  
  public function executeShow(sfWebRequest $request)
  {
    $slug = $request->getParameter('slug');
    $this->Citoyen = Doctrine::getTable('Citoyen')->findOneBySlug($slug);
    $this->forward404Unless($this->Citoyen->is_active);
    $response = $this->getResponse();
    $response->setTitle('Profil de '.$this->Citoyen->login); 
  }
  
  // Inscription
  public function executeNew(sfWebRequest $request)
  {
    if (!$this->getUser()->isAuthenticated()) {
      $this->form = new InscriptionForm();
      if ($request->isMethod('post'))
      {        
        $this->form->bind($request->getParameter('citoyen'));
        
        if ($this->form->isValid())
        {
          $this->form->save();
          
          $this->Citoyen = Doctrine::getTable('Citoyen')->findOneByEmail($this->form->getValue('email'));
          $this->connexion($this->Citoyen);
          $this->Citoyen->activation_id = md5(time()*rand());
          $this->Citoyen->save();
          
          $this->getComponent('mail', 'send', 
            array('subject'=>'Inscription NosDéputés.fr', 
            'to'=>array($this->form->getValue('email')), 
            'partial'=>'inscription', 
            'mailContext'=>array('activation_id' => $this->Citoyen->activation_id) 
            ));
          
          $this->getUser()->setFlash('notice', 'Vous allez recevoir un email de confirmation. Pour finaliser votre inscription, veuillez cliquer sur le lien d\'activation contenu dans cet email.');
          $this->redirect('@homepage');
        }
      }
    }
    else if ($this->getUser()->isAuthenticated() and $this->getUser()->getAttribute('is_active') == true)
    {
      $slug = $this->getUser()->getAttribute('slug');
      $this->getUser()->setFlash('notice', 'Vous êtes déja inscrit');
      $this->redirect('@citoyen?slug='.$slug);
    }
    else
    {
      $this->getUser()->setFlash('notice', 'Vous allez recevoir un email de confirmation. Pour finaliser votre inscription, veuillez cliquer sur le lien d\'activation contenu dans cet email.');
      $this->redirect('@homepage');
    }
  }
  
  /* public function executeRenvoimailactivation(sfWebRequest $request)
  {
    $id = $request->getParameter('user_id');
    
    $this->Citoyen = Doctrine::getTable('Citoyen')->findOneById($id);
    
    if ($this->getUser()->isAuthenticated() and $this->getUser()->getAttribute('user_id') == $id)
    {
      $this->getComponent('mail', 'send', array(
                  'subject'=>'Inscription NosDéputés.fr', 
                  'to'=>array($this->Citoyen->email), 
                  'partial'=>'inscription', 
                  'mailContext'=>array('activation_id' => $this->Citoyen->activation_id) 
                  ));
      $this->getUser()->setFlash('notice', 'Un email de confirmation vient de vous être envoyé.');
      $this->redirect($request->getReferer());
    }
    else { $this->forward404(); }
  } */
  
  public function executeEdit(sfWebRequest $request)
  {
    if ($this->getUser()->isAuthenticated() and $this->getUser()->getAttribute('is_active') == true) {
    
      $user = Doctrine::getTable('Citoyen')->findOneById($this->getUser()->getAttribute('user_id'));
    
      $this->form = new EditUserForm($user);
      
      if ($request->isMethod('put'))
      {
        $this->form->bind($request->getParameter('citoyen'));
        
        if ($this->form->isValid())
        {
          $this->form->save();
          $this->getUser()->setFlash('notice', 'Vous avez modifié votre profil avec succès');
          $this->redirect('@citoyen?slug='.$user->slug);
        }
      }
    }
    else { $this->redirect('@signin'); }
  }
  
  public function executeActivation(sfWebRequest $request)
  {
    $this->activation_id = $request->getParameter('activation_id');
    
    if ($this->getUser()->isAuthenticated() and ($this->getUser()->getAttribute('is_active') == true)) 
    {
      $this->forward404();
    }
    
    if (Doctrine::getTable('Citoyen')->findOneByActivationId($this->activation_id))
    {
      $user = Doctrine::getTable('Citoyen')->findOneByActivationId($this->activation_id);
      
      if (!$user->is_active == true)
      {
        $this->form = new MotdepasseForm($user);
        
        if ($request->isMethod('put'))
        {
          $values = $request->getParameter('citoyen');
          $this->form->bind($values);
          
          if ($this->form->isValid())
          {
            $this->form->save();
            $user->is_active = true;
            $user->activation_id = null;
            $user->save();
            Doctrine_Query::create()  
            ->update('Commentaire')
            ->set('is_public', '?', true)
            ->where('citoyen_id = ?', $user->id)
            ->execute();
            if ($this->getUser()->isAuthenticated())
            {
              $this->getUser()->setAttribute('is_active', true);
              $this->getUser()->setFlash('notice', 'Votre compte a été activé avec succès');
              $this->redirect('@citoyen?slug='.$user->slug);
            }
            else
            {
              $this->connexion($user);
              $this->getUser()->setFlash('notice', 'Votre compte a été activé avec succès');
              $this->redirect('@citoyen?slug='.$user->slug);
            }
          }
        }
      }
      else
      {
        $this->forward404();
      }
    }
    else
    {
      $this->forward404();
    }
  }
  /* 
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
  } */
  
  // Connection
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
            if (sha1($this->form->getValue('password')) == $user->password)
            {
              $this->connexion($user);
              $this->getUser()->setFlash('notice', 'Vous vous êtes connecté avec succès.');
              $this->redirect($request->getReferer());
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
      $this->redirect($request->getReferer());
    }
  }
  
  public function connexion($user)
  {
    // signin
    $this->getUser()->setAttribute('user_id', $user->getId());
    $this->getUser()->setAttribute('is_active', $user->getIsActive());
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
  
  public function executeSignout(sfWebRequest $request)
  {
    $this->deconnexion();
    $this->getUser()->setFlash('notice', 'Vous avez été déconnecté avec succès');
    $this->redirect($request->getReferer());
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
      $this->getUser()->setFlash('notice', 'Votre compte a été supprimé avec succès');
      $this->redirect('@homepage');
    }
    else
    {
      $this->getUser()->setFlash('error', 'Vous devez être connecté pour exécuter cette action');
      $this->redirect('@homepage');
    }
  }
  
}