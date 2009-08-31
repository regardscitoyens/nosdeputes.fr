<?php
class EditUserForm extends CitoyenForm
{
  public function configure()
  {
    // Enleve les widgets qu'on ne veut pas montrer
    unset(
      $this['id'], 
      $this['login'], 
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
    
    // Les labels
    $this->widgetSchema->setLabels(array(
      'pass' => 'Mot de passe',
     # 'pass_confirmation' => 'Répétez le mot de passe *',
      'email' => 'Email',
      'activite' => 'Activité',
      'naissance' => 'Date de naissance'
    ));
    
    $annees = range(1920, date('Y')); // array des dates depuis 1920
    $liste_annees = array_combine($annees, $annees); // array clés et valeurs des dates
      
    $this->widgetSchema['naissance'] = new sfWidgetFormDate(array('format' => '%day%/%month%/%year%', 'years' => $liste_annees));
    
    $this->widgetSchema->setNameFormat('citoyen[%s]');
  }
  
}
?>