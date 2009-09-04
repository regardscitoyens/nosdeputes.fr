<?php

class citoyenComponents extends sfComponents
{  
  
  // Connexion pour inscription mail
  public function executeConnexion()
  {
    if (isset($this->email)) {
      $user = Doctrine::getTable('Citoyen')->findOneByEmail($this->email);
      // signin
      $this->getUser()->setAttribute('user_id', $user->getId());
      $this->getUser()->setAttribute('is_active', $user->getIsActive());
      $this->getUser()->setAuthenticated(true);
      $this->getUser()->clearCredentials();
      // save last login
      $user->setLastLogin(date('Y-m-d H:i:s'));
      $user->save();
    }
  }

}

?>