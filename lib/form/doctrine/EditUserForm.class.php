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
   
    $profileForm = new sfGuardUserProfileForm($this->object->Profile);
    
    unset(
      $profileForm['id'], 
      $profileForm['sf_guard_user_id'], 
      $profileForm['username'], 
      $profileForm['employe_an'], 
      $profileForm['travail_pour'], 
      $profileForm['activation_id'], 
      $profileForm['nom_circo'], 
      $profileForm['num_circo'], 
      $profileForm['photo'],  
      $profileForm['created_at'], 
      $profileForm['updated_at'], 
      $profileForm['slug']
    );
    
    $annees = range(1920, date('Y')); // array des dates depuis 1920
    $liste_annees = array_combine($annees, $annees); // array clés et valeurs des dates
      
    $profileForm->widgetSchema['naissance'] = new sfWidgetFormDate(array('years' => $liste_annees));
    
    // Les labels du citoyen
    $profileForm->widgetSchema->setLabels(array(
      'sfGuardUserProfile' => '',
      'profession' => 'Activite',
      'naissance' => 'Date de naissance'
    ));
    
    $profileForm->validatorSchema['email'] = new sfValidatorEmail();

    $this->embedForm('Profile', $profileForm);
  }
  
}
?>