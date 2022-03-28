<?php

/**
 * Section form base class.
 *
 * @method Section getObject() Returns the current form's model object
 *
 * @package    senat
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedInheritanceTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseSectionForm extends ObjectCommentableForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['md5'] = new sfWidgetFormInputText();
    $this->validatorSchema['md5'] = new sfValidatorString(array('max_length' => 36, 'required' => false));

    $this->widgetSchema   ['titre'] = new sfWidgetFormTextarea();
    $this->validatorSchema['titre'] = new sfValidatorString(array('max_length' => 256, 'required' => false));

    $this->widgetSchema   ['titre_complet'] = new sfWidgetFormTextarea();
    $this->validatorSchema['titre_complet'] = new sfValidatorString(array('max_length' => 512, 'required' => false));

    $this->widgetSchema   ['section_id'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Section'), 'add_empty' => true));
    $this->validatorSchema['section_id'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Section'), 'required' => false));

    $this->widgetSchema   ['min_date'] = new sfWidgetFormInputText();
    $this->validatorSchema['min_date'] = new sfValidatorString(array('max_length' => 15, 'required' => false));

    $this->widgetSchema   ['max_date'] = new sfWidgetFormDate();
    $this->validatorSchema['max_date'] = new sfValidatorDate(array('required' => false));

    $this->widgetSchema   ['timestamp'] = new sfWidgetFormInputText();
    $this->validatorSchema['timestamp'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['nb_interventions'] = new sfWidgetFormInputText();
    $this->validatorSchema['nb_interventions'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['id_dossier_institution'] = new sfWidgetFormInputText();
    $this->validatorSchema['id_dossier_institution'] = new sfValidatorString(array('max_length' => 255, 'required' => false));

    $this->widgetSchema   ['created_at'] = new sfWidgetFormDateTime();
    $this->validatorSchema['created_at'] = new sfValidatorDateTime();

    $this->widgetSchema   ['updated_at'] = new sfWidgetFormDateTime();
    $this->validatorSchema['updated_at'] = new sfValidatorDateTime();

    $this->widgetSchema->setNameFormat('section[%s]');
  }

  public function getModelName()
  {
    return 'Section';
  }

}
