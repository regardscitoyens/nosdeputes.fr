<?php
class InscriptioncomForm extends CitoyenForm
{
  public function configure()
  {
    $this->widgetSchema->setNameFormat('citoyen[%s]');
    
    // Enleve les widgets qu'on ne veut pas montrer
    unset(
      $this['id'], 
      $this['login'],
      $this['pass'],
      $this['activite'],
      $this['naissance'],
      $this['sexe'],
      $this['employe_an'],
      $this['travail_pour'],
      $this['nom_circo'],
      $this['num_circo'],
      $this['photo'],
      $this['is_active'],
      $this['activation_id'],
      $this['role'],
      $this['last_login'],
      $this['created_at'],
      $this['updated_at'],
      $this['slug']
    );
    
    $this->validatorSchema['email'] = new sfValidatorEmail(array(), array('invalid' => 'Adresse email invalide.'));

    // labels
    $this->widgetSchema->setLabels(array(
      'email' => 'Email'
    ));
  }
}
?>