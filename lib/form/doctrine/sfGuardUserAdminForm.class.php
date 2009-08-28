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
   
    $profileForm = new sfGuardUserProfileForm($this->object->Profile);
    unset(
      $profileForm['id'], 
      $profileForm['sf_guard_user_id'], 
      $profileForm['username'], 
      $profileForm['slug'], 
      $profileForm['created_at'], 
      $profileForm['updated_at']
      );
    
    $annees = range(1920, date('Y')); // array des dates depuis 1920
    $liste_annees = array_combine($annees, $annees); // array clés et valeurs des dates
      
    $profileForm->widgetSchema['naissance'] = new sfWidgetFormDate(array('years' => $liste_annees));
    $profileForm->validatorSchema['email'] = new sfValidatorEmail();
    
    $this->embedForm('Profile', $profileForm);
  }
}