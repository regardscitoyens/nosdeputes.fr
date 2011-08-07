<?php
class ResetMotdepasseForm extends BaseForm
{
  public function configure()
  {
    $this->widgetSchema->setNameFormat('reset[%s]');
    
    $this->widgetSchema['login'] = new sfWidgetFormInputText();
    $this->validatorSchema['login'] = new sfValidatorString(array('required' => true), array('required' => 'Champ obligatoire'));
  
    $this->widgetSchema['code'] = new sfWidgetFormInputText();
    $this->validatorSchema['code'] = new sfValidatorString(array('required' => true), array('required' => 'Vous devez recopier le code de sécurité'));
    
    // label
    $this->widgetSchema->setLabels(array(
      'login' => 'Nom d\'utilisateur ou Email'
    ));
    
  }
}
?>