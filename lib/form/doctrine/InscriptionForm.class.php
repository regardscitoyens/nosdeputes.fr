<?php
class InscriptionForm extends CitoyenForm
{
  public function configure()
  {
    $this->widgetSchema->setNameFormat('citoyen[%s]');
    
    // Enleve les widgets qu'on ne veut pas montrer
    unset(
      $this['id'], 
      $this['password'], 
      $this['activite'], 
      $this['url_site'], 
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
    
    #$this->widgetSchema->setOption('form_formatter', 'list');
    
    $this->widgetSchema['login'] = new sfWidgetFormInput();
    $this->validatorSchema['login'] = new sfValidatorString(array('required' => true, 'min_length' => 4, 'max_length' => 40), array('invalid' => 'Adresse email invalide.', 'required' => 'Indiquez le nom d\'utilisateur souhaité', 'min_length' => '"%value%" est trop court (%min_length% caractères minimum).', 'max_length' => '"%value%" est trop long (%max_length% caractères maximum).'));
		
    $this->validatorSchema['email'] = new sfValidatorEmail(array(), array('invalid' => 'Adresse email invalide.', 'required' => 'Indiquez votre adresse email', ));
    
    // Les labels
    $this->widgetSchema->setLabels(array(
      'login' => 'Nom d\'utilisateur',
      'email' => 'Email'
    ));
    
  }
}
?>