<?php
class InscriptionForm extends BaseForm
{
  public function configure()
  {
    $this->widgetSchema->setNameFormat('citoyen[%s]');
    
    #$this->widgetSchema->setOption('form_formatter', 'list');  'unique_error' => '"%value%" existe déjà', 
    
    $this->widgetSchema['login'] = new sfWidgetFormInputText();
    $this->validatorSchema['login'] = new sfValidatorString(array('required' => true, 'min_length' => 4, 'max_length' => 40), array('invalid' => 'Ce nom d\'utilisateur existe déjà.',
    'required' => 'Indiquez le nom d\'utilisateur souhaité', 
    'min_length' => '"%value%" est trop court (%min_length% caractères minimum).', 
    'max_length' => '"%value%" est trop long (%max_length% caractères maximum).'));
    
    $this->widgetSchema['email'] = new sfWidgetFormInputText();
    $this->validatorSchema['email'] = new sfValidatorEmail(array('required' => true), array('invalid' => 'Adresse email invalide.', 'required' => 'Indiquez votre adresse email', ));
    
    $this->validatorSchema['login'] = new sfValidatorRegex(array('pattern' => '/^[^<\"]+$/'), array('invalid'=>'Seul du texte est autorisé pour ce champ'));

    // Les labels
    $this->widgetSchema->setLabels(array(
      'login' => 'Nom d\'utilisateur',
      'email' => 'Email'
    ));
    
  }
}
?>
