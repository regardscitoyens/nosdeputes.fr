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
    
    $annees = range(1920, date('Y')); // array des dates depuis 1920
    $liste_annees = array_combine($annees, $annees); // array clés et valeurs des dates
      
    $this->widgetSchema['naissance'] = new sfWidgetFormDate(array('format' => '%day%/%month%/%year%', 'years' => $liste_annees));
    
    $this->widgetSchema->setNameFormat('citoyen[%s]');
    
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