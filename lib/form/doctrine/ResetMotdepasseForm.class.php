<?php
class ResetMotdepasseForm extends sfForm
{
  public function configure()
  {
    $this->widgetSchema->setNameFormat('reset[%s]');
    
    $this->widgetSchema['login'] = new sfWidgetFormInput();
    $this->validatorSchema['login'] = new sfValidatorString(array('required' => false));
    
    // label
    $this->widgetSchema->setLabels(array(
      'login' => 'Nom d\'utilisateur ou Email'
    ));
    
  }
}
?>