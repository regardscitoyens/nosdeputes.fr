<?php
class ResetMotdepasseForm extends sfForm
{
  public function configure()
  {
    $this->widgetSchema->setNameFormat('reset[%s]');
    
    $this->widgetSchema['login'] = new sfWidgetFormInput();
    $this->validatorSchema['login'] = new sfValidatorString(array('required' => false));
  
    $this->widgetSchema['code'] = new sfWidgetFormInput();
    $this->validatorSchema['code'] = new sfValidatorString(array('required' => true), array('required' => 'Vous devez recopier le code de sécurité'));
    
    // label
    $this->widgetSchema->setLabels(array(
      'login' => 'Nom d\'utilisateur ou Email'
    ));
    
  }
}
?>