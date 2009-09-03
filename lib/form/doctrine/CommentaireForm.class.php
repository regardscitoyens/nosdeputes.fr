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
    $this['citoyen_id'],
    $this['updated_at'],
    $this['created_at'],
    $this['is_public'],
    $this['rate'],
    $this['object_type'],
    $this['lien'],
    $this['presentation'],
    $this['object_id'],
    $this['parlementaires_list']
    );
    $this->validatorSchema['commentaire'] = new sfValidatorString(array('required' => true, 'min_length'=>10), array('required' => 'Ce champ est obligatoire', 'min_length' => 'Le commentaire trop court, il doit faire au moins %min_length% caractères'));
    
    if (!sfContext::getInstance()->getUser()->isAuthenticated()) 
    {
      parent::configure();
      $mailForm = new InscriptioncomForm($this->object->Citoyen);
      $this->embedForm('Citoyen', $mailForm);
    }
  }
}