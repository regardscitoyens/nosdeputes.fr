<?php

class citoyenComponents extends sfComponents
{
  public function executeShortCitoyen()
  {
    if ($this->citoyen_id) {
      $this->user = Doctrine::getTable('Citoyen')->findOneById($this->citoyen_id);
      return;
    }
    else { $this->user = null; return; }
  }
}

?>