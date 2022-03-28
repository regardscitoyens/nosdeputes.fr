<?php

/**
 * Commentaire form base class.
 *
 * @method Commentaire getObject() Returns the current form's model object
 *
 * @package    senat
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedInheritanceTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseCommentaireForm extends ObjectRatedForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['citoyen_id'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Citoyen'), 'add_empty' => true));
    $this->validatorSchema['citoyen_id'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Citoyen'), 'required' => false));

    $this->widgetSchema   ['commentaire'] = new sfWidgetFormTextarea();
    $this->validatorSchema['commentaire'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['is_public'] = new sfWidgetFormInputCheckbox();
    $this->validatorSchema['is_public'] = new sfValidatorBoolean(array('required' => false));

    $this->widgetSchema   ['ip_address'] = new sfWidgetFormTextarea();
    $this->validatorSchema['ip_address'] = new sfValidatorString(array('max_length' => 512, 'required' => false));

    $this->widgetSchema   ['object_type'] = new sfWidgetFormInputText();
    $this->validatorSchema['object_type'] = new sfValidatorString(array('max_length' => 64, 'required' => false));

    $this->widgetSchema   ['object_id'] = new sfWidgetFormInputText();
    $this->validatorSchema['object_id'] = new sfValidatorString(array('max_length' => 16, 'required' => false));

    $this->widgetSchema   ['lien'] = new sfWidgetFormInputText();
    $this->validatorSchema['lien'] = new sfValidatorString(array('max_length' => 128, 'required' => false));

    $this->widgetSchema   ['presentation'] = new sfWidgetFormTextarea();
    $this->validatorSchema['presentation'] = new sfValidatorString(array('max_length' => 512, 'required' => false));

    $this->widgetSchema   ['created_at'] = new sfWidgetFormDateTime();
    $this->validatorSchema['created_at'] = new sfValidatorDateTime();

    $this->widgetSchema   ['updated_at'] = new sfWidgetFormDateTime();
    $this->validatorSchema['updated_at'] = new sfValidatorDateTime();

    $this->widgetSchema->setNameFormat('commentaire[%s]');
  }

  public function getModelName()
  {
    return 'Commentaire';
  }

}
