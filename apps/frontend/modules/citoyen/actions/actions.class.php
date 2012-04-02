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
    $this->order = $request->getParameter('order');
    if (!$this->order || !(preg_match('/^(alpha|comm|date|last)$/', $this->order)))
      $this->order = 'date';
    $datecom = "";
    if ($this->order === "last")
      $datecom = ", max(co.created_at) as date";
    $query = Doctrine::getTable('Citoyen')
      ->createQuery('c')
      ->select('c.*, sum(co.is_public) as nb_comment'.$datecom)
      ->leftJoin('c.Commentaires co')
      ->where('c.is_active = ?', true)
      ->groupBy('c.id');
    if ($this->order === "date") {
      $this->title = 'Les derniers citoyens inscrits';
      $query->orderBy('c.created_at DESC');
    } else if ($this->order === "comm") {
      $this->title = 'Les citoyens ayant le plus commenté';
      $query->orderBy('nb_comment DESC');
    } else if ($this->order === "alpha") {
      $this->title = 'Les citoyens inscrits';
      $query->orderBy('c.login');
    } else if ($this->order === "last") {
      $this->title = 'Les derniers citoyens ayant commenté';
      $query->orderBy('date desc');
    }
    $this->pager = Doctrine::getTable('Citoyen')->getPager($request, $query);
    $this->citoyens = $query->execute();
    $this->getResponse()->setTitle($this->title." sur NosDéputés.fr");
    $this->comments = Doctrine_Query::create()
      ->select('count(distinct(citoyen_id)) as auteurs, count(distinct(id)) as comments')
      ->from('Commentaire')
      ->where('is_public = 1')
      ->fetchOne();
  }
  
  public function executeNotauthorized(sfWebRequest $request) 
  {
    $this->getUser()->setFlash('notice', 'Vous ne pouvez pas accéder à cette page');
    $this->getResponse()->setStatusCode(403);
  }

  public function executeRedirect(sfWebRequest $request) {
    $user = Doctrine::getTable('Citoyen')->find($request->getParameter('id'));
    $this->forward404Unless($user);
    $this->redirect('@citoyen?slug='.$user->slug);
  }

  public function executeShow(sfWebRequest $request)
  {
    $slug = $request->getParameter('slug');
    $this->user = Doctrine::getTable('Citoyen')->findOneBySlug($slug);
    $this->forward404Unless($this->user->is_active);
    $response = $this->getResponse();
    $response->setTitle('Profil de '.$this->user->login); 
    $this->getUser()->setAttribute('token', md5(microtime(true) . mt_rand(0,10000)));
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
          $this->getUser()->setAttribute('partial', 'inscription');
          if (!myUser::CreateAccount($this->form->getValue('login'), $this->form->getValue('email'), $this))
          { return;}
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
  
  public function executeEdit(sfWebRequest $request)
  {
    if ($this->getUser()->isAuthenticated() and $this->getUser()->getAttribute('is_active') == true) {
    
      $this->user = Doctrine::getTable('Citoyen')->findOneById($this->getUser()->getAttribute('user_id'));
    
      $this->form = new EditUserForm($this->user);
      
      if ($request->isMethod('put'))
      {
        $this->form->bind($request->getParameter('citoyen'));
        
        if ($this->form->isValid())
        {
          $this->form->save();
          $this->getUser()->setFlash('notice', 'Vous avez modifié votre profil avec succès');
          $this->redirect('@citoyen?slug='.$this->getUser()->getAttribute('slug'));
        }
      }
    }
    else { $this->redirect('@signin'); }
  }
  
  public function executeEditpassword(sfWebRequest $request)
  {
    if ($this->getUser()->isAuthenticated() and $this->getUser()->getAttribute('is_active') == true) {
    
      $this->user = Doctrine::getTable('Citoyen')->findOneById($this->getUser()->getAttribute('user_id'));
    
      $this->form = new ChangeMotdepasseForm();
      
      if ($request->isMethod('post'))
      {
        $this->form->bind($request->getParameter('citoyen'));
        
        if ($this->form->isValid())  
        {
          if (sha1($this->form->getvalue('ancienpassword')) != $this->user->password)
          { 
            $this->getUser()->setFlash('error', 'Veuillez indiquer votre ancien mot de passe');
            return;
          }
          if ($this->form->getvalue('password') != $this->form->getvalue('password_bis'))
          {
            $this->getUser()->setFlash('error', 'Les 2 champs doivent être identiques');
            return;
          }
          $this->user->password = $this->form->getvalue('password');
          $this->user->save();
          $this->getUser()->setFlash('notice', 'Vous avez modifié votre mot de passe avec succès');
          $this->redirect('@citoyen?slug='.$this->getUser()->getAttribute('slug'));
        }
      }
    }
    else { $this->redirect('@signin'); }
  }
  
  public function executeActivation(sfWebRequest $request)
  {
    $this->slug = $request->getParameter('slug');
    $this->activation_id = $request->getParameter('activation_id');
    
    if ($this->getUser()->isAuthenticated() and $this->getUser()->getAttribute('is_active') != 0)
    {
      $userslug = $this->getUser()->getAttribute('slug');
      if ($userslug === $this->slug)
        $this->getUser()->setFlash('notice', 'Vous avez déjà activé votre compte.');
      else {
        sfProjectConfiguration::getActive()->loadHelpers(array('Url'));
        $this->getUser()->setFlash('notice', 'Vous êtes déjà connecté en tant que '.$this->getUser()->getAttribute('login').'. Pour activer le compte '.$this->slug.', veuillez d\'abord <a href="'.url_for('@signout').'">vous déconnecter</a>, puis cliquer de nouveau sur le lien dans l\'e-mail que vous avez reçu.');
      }
      $this->redirect('@citoyen?slug='.$userslug);
    }
    
    if (Doctrine::getTable('Citoyen')->findOneBySlug($this->slug))
    {
    self::setmotdepasse($this, $request);
    }
    else
    {
      $this->forward404();
    }
  }
  
  private function setmotdepasse($action, $request)
  {
    $user = Doctrine::getTable('Citoyen')->findOneBySlug($action->slug);
    if (!$user) $action->forward404();
    if ($user->activation_id == $action->activation_id)
    {
      $action->form = new MotdepasseForm();
      
      if ($request->isMethod('post'))
      {
        $action->form->bind($request->getParameter('citoyen'));
        
        if ($action->form->isValid())
        {
          if ($action->form->getvalue('password') != $action->form->getvalue('password_bis'))
          {
            $action->getUser()->setFlash('error', 'Les 2 champs doivent être identiques');
            return;
          }
          $user->password = $action->form->getvalue('password');
          if ($user->is_active == 0) { $user->is_active = true; $msg = 'Votre compte a été activé avec succès.'; } 
          else { $msg = 'Votre mot de passe a été réinitialisé avec succès.'; }
          $user->activation_id = null;
          $user->save();
      
          $commentaires = Doctrine::getTable('Commentaire')->createQuery('c')
          ->where('is_public = 0')
          ->andWhere('citoyen_id = ?', $user->id)
          ->execute();
          foreach ($commentaires as $c)
          {
            $c->is_public = 1;
            $c->save();
            $c->updateNbCommentaires();
          }
          if ($action->getUser()->isAuthenticated()) { $action->getUser()->setAttribute('is_active', $user->is_active); }
          else { myUser::SignIn($user->getLogin(), $action->form->getvalue('password'), false, $action); }
          $action->getUser()->setFlash('notice', $msg);
          $action->redirect('@citoyen?slug='.$action->slug);
        }
      }
    }
    else
    {
      if($user->activation_id != null)
      {
        $user->activation_id = null;
        $user->save();
        $action->getUser()->setFlash('notice', 'Une erreur s\'est produite');
      } else
        $action->getUser()->setFlash('notice', 'Ce compte a déjà été activé');
      $action->redirect('@citoyen?slug='.$action->slug);
    }
  }
  
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
          myUser::SignIn($this->form->getValue('login'), $this->form->getValue('password'), $this->form->getValue('remember'), $this);
          $this->redirect($request->getReferer());
        }
      }
    }
    else
    {
      $slug = $this->getUser()->getAttribute('slug');
      $this->getUser()->setFlash('notice', 'Vous êtes connecté');
      $this->redirect('@citoyen?slug='.$slug);
    }
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
    sfContext::getInstance()->getResponse()->setCookie('remember', '');
  }
  
  public function executeDelete(sfWebRequest $request)
  {
    if ($this->getUser()->isAuthenticated())  
    {
      if($this->getUser()->getAttribute('token') != $request->getParameter('token'))
        $this->getUser()->setFlash('error','Jeton incorrect. Essayez de recharger la page d\'ou vous venez.');
      else
      {
        $user = Doctrine::getTable('Citoyen')->findOneById($this->getUser()->getAttribute('user_id'));
        $user->delete();
        $this->deconnexion();
        $this->getUser()->setFlash('notice', 'Votre compte a été supprimé avec succès');
      }
    }
    else
      $this->getUser()->setFlash('error', 'Vous devez être connecté pour exécuter cette action');
    $this->redirect('@homepage');
  }
  
  public function executeUploadavatar(sfWebRequest $request)
  {
    if ($this->getUser()->isAuthenticated() and $this->getUser()->getAttribute('is_active') == true) {
    
      $this->user = Doctrine::getTable('Citoyen')->findOneById($this->getUser()->getAttribute('user_id'));
    
      $this->form = new UploadAvatarForm();
      
      if ($request->isMethod('post'))
      {
        $this->form->bind($request->getParameter('upload'), $request->getFiles('upload'));
        
        if ($this->form->isValid() and $this->form->getValue('photo'))
        {
          $file = $this->form->getValue('photo');
          
          $photo = $file->getTempName();
          list($largeur_source, $hauteur_source) = getimagesize($photo);

          $largeur = $hauteur = 100;
          if ($largeur_source >= $hauteur_source) { $hauteur = round($hauteur_source * $largeur / $largeur_source); }
          else { $largeur = round($largeur_source * $hauteur / $hauteur_source); }
          
          $source = imagecreatefromstring(file_get_contents($photo));
          $destination = imagecreatetruecolor(100, 100);
          $white = imagecolorallocate($destination, 255, 255, 255);
          imagefilledrectangle($destination, 0, 0, 100, 100, $white);
          imagecopyresampled($destination, $source, (100-$largeur)/2, (100-$hauteur)/2, 0, 0, $largeur, $hauteur, $largeur_source, $hauteur_source);
          
          imagejpeg($destination, $photo);
          $this->user->photo = file_get_contents($photo);
          $this->user->save();
          $this->getUser()->setFlash('notice', 'Vous avez modifié votre profil avec succès');
          return $this->redirect('@edit_citoyen');
        }
        else
        {
          $this->getUser()->setFlash('error', 'Veuillez indiquer votre photo/avatar');
        }
      }
    }
    else { $this->redirect('@signin'); }
  }
  
  public function executePhoto($request)
  {
    $slug = $request->getParameter('slug');
    $this->user = Doctrine::getTable('Citoyen')->findOneBySlug($slug);
    if (!$this->user || !$this->user->photo) {
      return $this->redirect('http://'.$_SERVER['HTTP_HOST'].'/images/xneth/avatar_citoyen.png');
    }
    $this->setLayout(false);
    $this->getResponse()->setHttpHeader('content-type', 'image/jpeg');
    $this->getResponse()->addCacheControlHttpHeader('max_age=60');
    $this->getResponse()->setHttpHeader('Expires', $this->getResponse()->getDate(time()*2));
  }
  
  public function executeResetmotdepasse(sfWebRequest $request)
  {
    $this->slug = $request->getParameter('slug');
    $this->activation_id = $request->getParameter('activation_id');
    if (Doctrine::getTable('Citoyen')->findOneBySlug($this->slug)->is_active < 0) {
      $this->getUser()->setFlash('error', 'Ce compte a été désactivé');
      return;
    }
    if ($this->getUser()->isAuthenticated())
    {
      $user = Doctrine::getTable('Citoyen')->findOneBySlug($this->getUser()->getAttribute('slug'));
      self::sendmailresetmotdepasse($user, $this);
    }
    else if ($this->slug and $this->activation_id)
    {
      if (Doctrine::getTable('Citoyen')->findOneBySlug($this->slug))
      {
        self::setmotdepasse($this, $request);
      }
    }
    else
    {
      $this->first = true;
      $this->form = new ResetMotdepasseForm();
        
      if ($request->isMethod('post'))
      {
        $this->form->bind($request->getParameter('reset'));
        
        if ($this->form->isValid())
        {
          $login = $this->form->getValue('login');
      
          if($this->form->getValue('code') != $this->getUser()->getAttribute('codesecu'))
          {
            $this->getUser()->setFlash('error', 'Le code de sécurité ne correspond pas');
            return;
          }
      
          if ($login)
          {
            if (Doctrine::getTable('Citoyen')->findOneByEmail($login))
            {
              $user = Doctrine::getTable('Citoyen')->findOneByEmail($login);
            }
            else if (Doctrine::getTable('Citoyen')->findOneByLogin($login))
            {
              $user = Doctrine::getTable('Citoyen')->findOneByLogin($login);
            }
            else
            {
              $this->getUser()->setFlash('error', 'Aucun utilisateur enregistré ne correspond');
              return;
            }
          }
          else
          {
            $this->getUser()->setFlash('error', 'Veuillez indiquer votre nom d\'utilisateur <strong>ou</strong> votre email');
            return;
          }
          self::sendmailresetmotdepasse($user, $this);
        }
      }
    }
  }
  
  private function sendmailresetmotdepasse($user, $action)
  {
    $activation_id = md5(time()*rand());
    $user->activation_id = $activation_id;
    $user->save();
	
    $action->getComponent('mail', 'send', array(
    'subject'=>'Réinitialisation de votre mot de passe - NosDéputés.fr', 
    'to'=>array($user->email), 
    'partial'=>'resetmotdepasse', 
    'mailContext'=>array('activation_id' => $activation_id, 'slug' => $user->slug)
    ));
    
    $action->getUser()->setFlash('notice', 'Un email de réinitialisation de mot de passe vient de vous être envoyé.<br />Si vous rencontrez un problème lors de cette procédure veuillez nous contacter par email à l\'adresse contact[at]regardscitoyens.org.');
    $action->redirect('@homepage');
  }

  public function executeConnected(sfWebRequest $request)
  {
    $this->setLayout(false);
    $this->getResponse()->setHttpHeader('content-type', 'text/plain');
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
  
}
