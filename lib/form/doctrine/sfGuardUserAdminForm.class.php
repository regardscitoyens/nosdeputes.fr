<?php

/**
 * sfGuardUserAdminForm for admin generators
 *
 * @package form
 * @package sf_guard_user
 */
class sfGuardUserAdminForm extends BasesfGuardUserAdminForm
{
  public function configure()
  {
    // Enleve les widgets qu'on ne veut pas montrer
    unset(
      $this['username']
    );
		
		parent::configure();
   
    $citoyenForm = new CitoyenForm($this->object->Citoyen);
    unset(
		  $citoyenForm['id'], 
			$citoyenForm['sf_guard_user_id'], 
			$citoyenForm['username'], 
			$citoyenForm['slug'], 
      $citoyenForm['created_at'], 
      $citoyenForm['updated_at']
	    );
    $this->embedForm('Citoyen', $citoyenForm);
  }
}