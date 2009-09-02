<?php
class SigninmailForm extends sfForm
{
  public function configure()
  {
    $this->widgetSchema->setNameFormat('signinmail[%s]');
    
    $this->setWidgets(array(
      'email'    => new sfWidgetFormInput()
    ));
    
    $this->setValidators(array(
      'email' => new sfValidatorEmail(array('required' => true), array('invalid' => 'Vérifiez votre saisie.', 'required' => 'Veuillez indiquer votre email.'))
    ));

    // labels
    $this->widgetSchema->setLabels(array(
      'email' => 'Email'  
    ));
  }
}
?>