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
    if ($this->citoyen_id) {
		  $this->user = Doctrine::getTable('Citoyen')->findOneById($this->citoyen_id);
			#if ($this->user->is_active == false) { $this->user = null; }
      return;
    }
		else { return; }
  }
}

?>