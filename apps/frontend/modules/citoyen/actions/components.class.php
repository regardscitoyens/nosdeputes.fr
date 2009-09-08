<?php

class citoyenComponents extends sfComponents
{  
  
  // Connexion pour inscription mail
  public function executeConnexion()
  {
    if (isset($this->login)) {
      $user = Doctrine::getTable('Citoyen')->findOneByLogin($this->login);
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
    }
  }

  public function executeShortCitoyen()
  {
    $this->user = null;
    if ($this->citoyen_id) {
      return ;
    }else{
      $this->user = Doctrine::getTable('Citoyen')->find($this->citoyen_id);
      return;
    }
  }
}

?>