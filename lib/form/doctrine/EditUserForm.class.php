<?php
class EditUserForm extends CitoyenForm  
{
  public function configure()
  {
    // Enleve les widgets qu'on ne veut pas montrer
    unset(
      $this['id'], 
      $this['login'], 
      $this['password'], 
      $this['email'], 
      $this['photo'], 
      $this['employe_an'], 
      $this['travail_pour'], 
      $this['nom_circo'], 
      $this['num_circo'], 
      $this['is_active'], 
      $this['activation_id'], 
      $this['role'], 
      $this['last_login'], 
      $this['created_at'], 
      $this['updated_at'], 
      $this['slug']
    );
    
    $annees = range(1920, date('Y')); // array des dates depuis 1920
    $liste_annees = array_combine($annees, $annees); // array clés et valeurs des dates
      
    $this->widgetSchema['naissance'] = new sfWidgetFormDate(array('format' => '%day%/%month%/%year%', 'years' => $liste_annees));
    
    $this->widgetSchema['url_site'] = new sfWidgetFormInputText();
    $this->validatorSchema['url_site'] = new sfValidatorUrl(array('required' => false), array('invalid' => 'l\'url doit être de la forme "http://www.monsite.fr"'));
    
    $this->validatorSchema['activite'] = new sfValidatorRegex(array('pattern' => '/^[^<\"]+$/'), array('invalid'=>'Seul du texte est autorisé pour ce champ'));
    
    // Les labels
    $this->widgetSchema->setLabels(array(
      'activite' => 'Activité : ', 
      'url_site' => 'Site web : ', 
      'naissance' => 'Date de naissance : ', 
      'sexe' => 'Civilité : '
    ));
  }
  
}
?>
