<?php

class myUser extends sfBasicSecurityUser
{
  public static function CreateAccount($nom, $email, $action)
  {
    if (Doctrine::getTable('Citoyen')->findOneByLogin($nom)) {
      $action->getUser()->setFlash('error', 'Ce nom d\'utilisateur existe déjà.');
      return false;
    }

    if (Doctrine::getTable('Citoyen')->findOneByEmail($email)) {
      $action->getUser()->setFlash('error', 'Cette adresse email existe déjà.');
      return false;
    }
    
    $citoyen = new Citoyen;
    $citoyen->login = $nom;
    $citoyen->email = $email;
    $citoyen->activation_id = md5(time()*rand());
    $citoyen->save();
    echo $action->getComponent('citoyen', 'connexion', array('login' => $citoyen->login));
    echo $action->getComponent('mail', 'send', array(
      'subject'=>'Inscription NosDéputés.fr', 
      'to'=>array($citoyen->email), 
      'partial'=>'inscriptioncom', 
      'mailContext'=>array('slug' => sfContext::getInstance()->getUser()->getAttribute('slug'), 'activation_id' => $citoyen->activation_id) 
      ));
    return $citoyen->getId();
  }

  public static function SignIn($login, $password, $remember, $action) 
  {
    if (! Doctrine::getTable('Citoyen')->findOneByLogin($login)) {
      sleep(3);
      $action->getUser()->setFlash('error', 'Utilisateur ou mot de passe incorrect');
      return;
    }
    $user = Doctrine::getTable('Citoyen')->findOneByLogin($login);
    if (!$user->isPasswordCorrect($password)) {
      sleep(3);
      $action->getUser()->setFlash('error', 'Utilisateur ou mot de passe incorrect');
      return;
    }
    $action->getComponent('citoyen', 'connexion', array('login' => $user->login));
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

}
