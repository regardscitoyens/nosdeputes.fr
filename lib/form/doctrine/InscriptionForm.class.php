<?php
class InscriptionForm extends CitoyenForm
{
  public function configure()
  {
    // Enleve les widgets qu'on ne veut pas montrer
    unset(
      $this['id'], 
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
    $this->validatorSchema['login']->setOption('required', true);
    
    // verif mot de passe avec confirmation
    $this->widgetSchema['pass'] = new sfWidgetFormInputPassword();
    $this->validatorSchema['pass']->setOption('required', true);
    /* $this->widgetSchema['pass_confirmation'] = new sfWidgetFormInputPassword();
    $this->validatorSchema['pass_confirmation'] = clone $this->validatorSchema['pass'];
 
    $this->widgetSchema->moveField('pass_confirmation', 'after', 'pass');
 
    $this->mergePostValidator(new sfValidatorSchemaCompare('pass', sfValidatorSchemaCompare::EQUAL, 'pass_confirmation', array(), array('invalid' => 'Le mot de passe ne correspond pas.')));
 */
    
    $this->validatorSchema['email'] = new sfValidatorEmail(array(), array('invalid' => 'Adresse email invalide.'));
    
    $annees = range(1920, date('Y')); // array des dates depuis 1920
    $liste_annees = array_combine($annees, $annees); // array clés et valeurs des dates
    $this->widgetSchema['naissance'] = new sfWidgetFormDate(array('years' => $liste_annees));
    
    // Les labels
    $this->widgetSchema->setLabels(array(
      'login' => 'Nom d\'utilisateur *',
      'pass' => 'Mot de passe *',
     # 'pass_confirmation' => 'Répétez le mot de passe *',
      'email' => 'Email *',
      'activite' => 'Activite',
      'naissance' => 'Date de naissance'
    ));
    
    $this->widgetSchema->setNameFormat('citoyen[%s]');
  }
}
?>