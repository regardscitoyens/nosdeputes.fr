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
    
    // Les labels du sfguarduser
    $this->widgetSchema->setLabels(array(
      'username' => 'Nom d\'utilisateur',
      'password' => 'Mot de passe',
      'password_confirmation' => 'Répétez le mot de passe',
    ));
    
    // inclus le formulaire du profil du citoyen
    parent::configure();
   
    $profileForm = new sfGuardUserProfileForm($this->object->Profile);
    
    unset(
      $profileForm['id'], 
      $profileForm['sf_guard_user_id'], 
      $profileForm['employe_an'], 
      $profileForm['travail_pour'], 
      $profileForm['nom_circo'], 
      $profileForm['num_circo'], 
      $profileForm['activation_id'], 
      $profileForm['photo'],  
      $profileForm['created_at'], 
      $profileForm['updated_at'], 
      $profileForm['slug']
    );
    
    $annees = range(1920, date('Y')); // array des dates depuis 1920
    $liste_annees = array_combine($annees, $annees); // array clés et valeurs des dates
      
    $profileForm->widgetSchema['naissance'] = new sfWidgetFormDate(array('years' => $liste_annees));
    $profileForm->widgetSchema['username'] = new sfWidgetFormInputHidden(array(), array('invalid' => ''));
    // Les labels du profil
    $profileForm->widgetSchema->setLabels(array(
      'sfGuardUserProfile' => '',
      'profession' => 'Activite',
      'naissance' => 'Date de naissance'
    ));
    
    $profileForm->validatorSchema['email'] = new sfValidatorEmail(array(), array('invalid' => 'Adresse email invalide.'));
      
    $this->embedForm('Profile', $profileForm);
  }
  
}
?>