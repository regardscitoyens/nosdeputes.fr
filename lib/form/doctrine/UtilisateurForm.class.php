<?php
/**
 * Utilisateur form.
 *
 * @package    form
 * @subpackage Utilisateur
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 6174 2007-11-27 06:22:40Z fabien $
 */
class UtilisateurForm extends BaseUtilisateurForm
{
  public function configure()
  {
		// unset Widgets
		unset($this->widgetSchema['id']);
		unset($this->widgetSchema['activation_id']);
		unset($this->widgetSchema['activation']);
		unset($this->widgetSchema['slug']);
		unset($this->widgetSchema['created_at']);
		unset($this->widgetSchema['updated_at']);
    
    // unset Validators  
		unset($this->validatorSchema['id']);
		unset($this->validatorSchema['activation_id']);
		unset($this->validatorSchema['activation']);
		unset($this->validatorSchema['slug']);
		unset($this->validatorSchema['created_at']);
		unset($this->validatorSchema['updated_at']);
  }
}
?>