<?php
class EditUserForm extends sfGuardUserForm
{
  public function configure()
  {
    // Enleve les widgets qu'on ne veut pas montrer
    unset(
      $this['username'],
      $this['password'],
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
		
		// Les labels du sfguarduser
		$this->widgetSchema->setLabels(array(
      'username' => 'Nom d\'utilisateur',
      'password' => 'Mot de passe',
      'password_confirmation' => 'Répétez le mot de passe',
    ));
		
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
			$citoyenForm['slug']
		);
		
		$annees = range(1920, date('Y')); // array des dates depuis 1920
		$liste_annees = array_combine($annees, $annees); // array clés et valeurs des dates
			
		$citoyenForm->widgetSchema['naissance'] = new sfWidgetFormDate(array('years' => $liste_annees));
		
		// Les labels du citoyen
		$citoyenForm->widgetSchema->setLabels(array(
			'citoyen' => '',
			'profession' => 'Profession/Occupation',
			'naissance' => 'Date de naissance'
		));
		
		$citoyenForm->validatorSchema['email'] = new sfValidatorEmail();

    $this->embedForm('Citoyen', $citoyenForm);
  }
	
}
?>