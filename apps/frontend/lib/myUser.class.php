<?php

class myUser extends sfBasicSecurityUser
{
  public static function CreateAccount($nom, $email, $action)
  {
    if(Doctrine::getTable('Citoyen')->findOneByLogin($nom)) {
      $action->getUser()->setFlash('error', 'Ce nom d\'utilisateur existe déjà.');
      return false;
    }

    if(Doctrine::getTable('Citoyen')->findOneByEmail($email)) {
      $action->getUser()->setFlash('error', 'Cette adresse email existe déjà.');
      return false;
    }
    
    $citoyen = new Citoyen;
    $citoyen->login = $nom;
    $citoyen->email = $email;
    $citoyen->activation_id = md5(time()*rand());
    $citoyen->save();

    $partial = $action->getUser()->getAttribute('partial');

    self::connexion($citoyen, $action);

    echo $action->getComponent('mail', 'send', array(
      'subject'=>'Inscription NosDéputés.fr', 
      'to'=>array($citoyen->email), 
      'partial'=>$partial, 
      'mailContext'=>array('slug' => $citoyen->slug, 'activation_id' => $citoyen->activation_id) 
      ));

    $action->getUser()->getAttributeHolder()->remove('partial');
    $action->getUser()->setFlash('notice', 'Vous allez recevoir un email de confirmation. Pour finaliser votre inscription, veuillez cliquer sur le lien d\'activation contenu dans cet email.');
    return $citoyen->getId();
  }

  public static function SignIn($login, $password, $remember, $action) 
  {
    sfProjectConfiguration::getActive()->loadHelpers(array('Url'));
	
    $reset_mdp = '<a href="'.url_for('@reset_mdp').'">Mot de passe oublié ?</a>';
    
    if(Doctrine::getTable('Citoyen')->findOneByLogin($login))
    {
      $user = Doctrine::getTable('Citoyen')->findOneByLogin($login);
    }
    else if(Doctrine::getTable('Citoyen')->findOneByEmail($login))
    {
      $user = Doctrine::getTable('Citoyen')->findOneByEmail($login);
    }
    else
    {
      $action->getUser()->setFlash('error', 'Utilisateur ou mot de passe incorrect<br />'.$reset_mdp);
      return;
    }
    if (!$user->isPasswordCorrect($password)) {
      $action->getUser()->setFlash('error', 'Utilisateur ou mot de passe incorrect<br />'.$reset_mdp);
      return;
    }
    
    if($user->activation_id != null)
    {
      $user->activation_id = null;
      $user->save();
    }
    
    self::connexion($user, $action);
    
    $action->getUser()->setFlash('notice', 'Vous vous êtes connecté avec succès.');
    
    if($remember)
    {
      $secret_key = sfConfig::get('app_secret_key');
      $expiration_cookie = sfConfig::get('app_expiration_cookie');
      $remember_key = $user->slug.'_'.sha1($secret_key.$user->slug);
      sfContext::getInstance()->getResponse()->setCookie('remember', $remember_key, $expiration_cookie, '/');
    }
    return $user->id;
  }
  
  protected static function connexion($user, $action)
  {
    // signin
    $action->getUser()->setAttribute('user_id', $user->getId());
    $action->getUser()->setAttribute('is_active', $user->getIsActive());
    $action->getUser()->setAttribute('login', $user->getLogin());
    $action->getUser()->setAttribute('slug', $user->getSlug());
    $action->getUser()->setAuthenticated(true);
    $action->getUser()->addCredentials($user->getRole());
    // save last login
    $user->setLastLogin(date('Y-m-d H:i:s'));
    $user->save();
  }

}
