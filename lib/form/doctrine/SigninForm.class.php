<?php
class SigninForm extends BaseForm
{
  public function configure()
  {
    $this->setWidgets(array(
      'login' => new sfWidgetFormInputText(),
      'password' => new sfWidgetFormInputPassword(),
      'remember' => new sfWidgetFormInputCheckbox()
    ));
    
    $this->widgetSchema->setNameFormat('signin[%s]');
    
    $this->setValidators(array(
      'login' => new sfValidatorString(array('required' => true), array('invalid' => 'Ce nom d\'utilisateur n\'existe pas.', 'required' => 'Veuillez indiquer votre nom d\'utilisateur.')),
      'password'  => new sfValidatorString(array('required' => true), array('invalid' => 'Le mot de passe ne correspond pas.', 'required' => 'Veuillez indiquer votre mot de passe.')),
      'remember'  => new sfValidatorBoolean(array('required' => false))
    ));   

    // labels
    $this->widgetSchema->setLabels(array(
      'login' => 'Nom d\'utilisateur ou Email : ', 
      'password' => 'Mot de passe : ', 
      'remember' => 'Se rappeler de moi : '
    ));
  }
}
?>