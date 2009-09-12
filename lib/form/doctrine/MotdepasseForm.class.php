<?php
class MotdepasseForm extends CitoyenForm
{
  public function configure()
  {
    $this->widgetSchema->setNameFormat('citoyen[%s]');
    
    // Enleve les widgets qu'on ne veut pas montrer
    unset(
      $this['id'], 
      $this['login'], 
      $this['email'], 
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
    
    // verif mot de passe avec confirmation
    $this->widgetSchema['password'] = new sfWidgetFormInputPassword();
    $this->widgetSchema['password_bis'] = new sfWidgetFormInputPassword();
    
    // Les labels
    $this->widgetSchema->setLabels(array(
      'password' => 'Mot de passe : ',
      'password_bis' => 'Confirmation : '
    ));
    
    $this->validatorSchema['password'] = new sfValidatorString(array('required' => true, 'min_length' => 6), array('min_length' => "Votre mot de passe est trop court, veuillez employer au moins 6 caractères s'il vous plaît.", 'required' => 'Champ obligatoire.'));
    $this->validatorSchema['password_bis'] = new sfValidatorString();

    $this->mergePostValidator(new sfValidatorSchemaCompare(
      'password',
      sfValidatorSchemaCompare::EQUAL,
      'password_bis',
      array(),
      array('invalid' => 'Les champs doivent être identiques.')
    ));
  }
}
?>