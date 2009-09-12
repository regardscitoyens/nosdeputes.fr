<?php

class citoyenComponents extends sfComponents
{  
  
  // Connexion pour inscription mail
  public function executeConnexion()
  {
  }

  public function executeShortCitoyen()
  {
    if ($this->citoyen_id) {
      $this->user = Doctrine::getTable('Citoyen')->findOneById($this->citoyen_id);
      #if ($this->user->is_active == false) { $this->user = null; }
      return;
    }
    else { $this->user = null; return; }
  }
}

?>