<?php
class MotdepasseForm extends BaseForm
{
  public function configure()
  {
    $this->widgetSchema->setNameFormat('citoyen[%s]');
    
    $this->widgetSchema['password'] = new sfWidgetFormInputPassword();
    $this->widgetSchema['password_bis'] = new sfWidgetFormInputPassword();
    
    // Les labels
    $this->widgetSchema->setLabels(array(
      'password' => 'Mot de passe : ',
      'password_bis' => 'Confirmation : '
    ));
    
    $this->validatorSchema['password'] = new sfValidatorString(array('required' => true, 'min_length' => 6), array('min_length' => "Votre mot de passe est trop court, veuillez employer au moins 6 caractères s'il vous plaît.", 'required' => 'Champ obligatoire'));
    $this->validatorSchema['password_bis'] = new sfValidatorString(array('required' => true), array('required' => 'Champ obligatoire'));
  }
}
?>