<?php
class EditUserForm extends CitoyenForm
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
    
    // Les labels
    $this->widgetSchema->setLabels(array(
      'sfGuardUserProfile' => '',
      'activite' => 'Activite',
      'naissance' => 'Date de naissance'
    ));
    
    $annees = range(1920, date('Y')); // array des dates depuis 1920
    $liste_annees = array_combine($annees, $annees); // array clés et valeurs des dates
      
    $this->widgetSchema['naissance'] = new sfWidgetFormDate(array('years' => $liste_annees));
    
    // Les labels du citoyen
    
    
    $this->validatorSchema['email'] = new sfValidatorEmail();
  }
  
}
?>