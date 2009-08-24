<?php
class InscriptionForm extends sfGuardUserForm
{
  public function configure()
  {
    // Enleve les widgets qu'on ne veut pas montrer
    unset(
      $this['is_active'],
      $this['is_super_admin'],
      $this['updated_at'],
      $this['groups_list'],
      $this['permissions_list'],
      $this['last_login'],
      $this['created_at'],
      $this['salt'],
      $this['algorithm']
    );
		
		#$this->widgetSchema->setOption('form_formatter', 'list');
		
    // verif mot de passe avec confirmation
    $this->widgetSchema['password'] = new sfWidgetFormInputPassword();
    $this->validatorSchema['password']->setOption('required', true);
    $this->widgetSchema['password_confirmation'] = new sfWidgetFormInputPassword();
    $this->validatorSchema['password_confirmation'] = clone $this->validatorSchema['password'];
 
    $this->widgetSchema->moveField('password_confirmation', 'after', 'password');
 
    $this->mergePostValidator(new sfValidatorSchemaCompare('password', sfValidatorSchemaCompare::EQUAL, 'password_confirmation', array(), array('invalid' => 'Le mot de passe ne correspond pas.')));
		
		// inclus le formulaire Citoyen
		parent::configure();
   
    $citoyenForm = new CitoyenForm($this->object->Citoyen);
		
    unset(
		  $citoyenForm['id'], 
			$citoyenForm['sf_guard_user_id'], 
			$citoyenForm['username'], 
			$citoyenForm['employe_an'], 
			$citoyenForm['travail_pour'], 
			$citoyenForm['activation_id'], 
			$citoyenForm['nom_circo'], 
			$citoyenForm['num_circo'], 
			$citoyenForm['photo'],  
			$citoyenForm['created_at'], 
			$citoyenForm['updated_at'], 
			$citoyenForm['slug']);
		
		/* $citoyenForm->setValidators(array(
      'email'   => new sfValidatorEmail()
    )); */

    $this->embedForm('Citoyen', $citoyenForm);
  }
	
}
?>