<?php
class SigninForm extends sfForm
{
  public function configure()
  {
    $this->setWidgets(array(
      'login'    => new sfWidgetFormInput(),
      'pass'   => new sfWidgetFormInputPassword()
    ));
    
    $this->widgetSchema->setNameFormat('signin[%s]');
    
    $this->setValidators(array(
      'login' => new sfValidatorString(array('required' => true), array('invalid' => 'Ce nom d\'utilisateur n\'existe pas.', 'required' => 'Veuillez indiquer votre nom d\'utilisateur.')),
      'pass'  => new sfValidatorString(array('required' => true), array('invalid' => 'Le mot de passe ne correspond pas.', 'required' => 'Veuillez indiquer votre mot de passe.'))
    ));

    // labels
    $this->widgetSchema->setLabels(array(
      'login' => 'Nom d\'utilisateur *',
      'pass' => 'Mot de passe *'
    ));
  }
}
?>