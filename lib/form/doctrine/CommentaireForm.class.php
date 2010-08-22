<?php

/**
 * Commentaire form.
 *
 * @package    form
 * @subpackage Commentaire
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 6174 2007-11-27 06:22:40Z fabien $
 */
class CommentaireForm extends BaseCommentaireForm
{
  public function configure()
  {
    unset(
    $this['id'],
    $this['citoyen_id'],
    $this['updated_at'],
    $this['created_at'],
    $this['is_public'],
    $this['ip_address'],
    $this['rate'],
    $this['object_type'],
    $this['lien'],
    $this['presentation'],
    $this['object_id'],
    $this['parlementaires_list']
    );
    $this->validatorSchema['commentaire'] = new sfValidatorString(array('required' => true, 'min_length'=>10), array('required' => 'Ce champ est obligatoire', 'min_length' => 'Le commentaire trop court, il doit faire au moins %min_length% caractères'));
    
    if (!sfContext::getInstance()->getUser()->isAuthenticated() || !$_GET['isAuthenticated']) 
    {
      $this->widgetSchema['nom'] = new sfWidgetFormInputText();
      $this->widgetSchema['email'] = new sfWidgetFormInputText();
      $this->widgetSchema['login'] = new sfWidgetFormInputText();
      $this->widgetSchema['password'] = new sfWidgetFormInputPassword();
      
      
      $this->validatorSchema['nom'] = new sfValidatorString(array('required' => false, 'min_length' => 4, 'max_length' => 40), array('invalid' => 'Ce nom d\'utilisateur existe déjà.', 'min_length' => '"%value%" est trop court (%min_length% caractères minimum).', 'max_length' => '"%value%" est trop long (%max_length% caractères maximum).'));
      $this->validatorSchema['email'] = new sfValidatorEmail(array('required' => false), array('invalid' => 'Adresse email invalide.'));
      $this->validatorSchema['login'] = new sfValidatorString(array('required' => false), array('invalid' => 'Ce nom d\'utilisateur n\'existe pas.'));
      $this->validatorSchema['password'] = new sfValidatorString(array('required' => false), array('invalid' => 'Le mot de passe ne correspond pas.'));
      
    // labels
    $this->widgetSchema->setLabels(array(
      'commentaire' => 'Ecrire un commentaire',
      'nom' => 'Nom d\'utilisateur',
      'email' => 'Email',
      'login' => 'Nom d\'utilisateur ou Email',
      'password' => 'Mot de passe',
    ));
    }
  }
}
