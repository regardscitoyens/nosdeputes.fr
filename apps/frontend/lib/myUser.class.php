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
    $action->getComponent('citoyen', 'connexion', array('login' => $citoyen->login));
    $action->getComponent('mail', 'send', array(
					      'subject'=>'Inscription NosDéputés.fr', 
					      'to'=>array($citoyen->email), 
					      'partial'=>'inscriptioncom', 
					      'mailContext'=>array('activation_id' => $citoyen->activation_id) 
					      ));
    return $citoyen->getId();
  }

  public static function SignIn($login, $password, $action) 
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
    return $user->id;
  }

}
