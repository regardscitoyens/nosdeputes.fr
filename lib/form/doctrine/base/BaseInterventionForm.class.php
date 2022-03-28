<?php

/**
 * Intervention form base class.
 *
 * @method Intervention getObject() Returns the current form's model object
 *
 * @package    senat
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedInheritanceTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseInterventionForm extends ObjectCommentableForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['nb_mots'] = new sfWidgetFormInputText();
    $this->validatorSchema['nb_mots'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['md5'] = new sfWidgetFormInputText();
    $this->validatorSchema['md5'] = new sfValidatorString(array('max_length' => 36, 'required' => false));

    $this->widgetSchema   ['intervention'] = new sfWidgetFormTextarea();
    $this->validatorSchema['intervention'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['timestamp'] = new sfWidgetFormInputText();
    $this->validatorSchema['timestamp'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['source'] = new sfWidgetFormInputText();
    $this->validatorSchema['source'] = new sfValidatorString(array('max_length' => 128, 'required' => false));

    $this->widgetSchema   ['seance_id'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Seance'), 'add_empty' => true));
    $this->validatorSchema['seance_id'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Seance'), 'required' => false));

    $this->widgetSchema   ['section_id'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Section'), 'add_empty' => true));
    $this->validatorSchema['section_id'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Section'), 'required' => false));

    $this->widgetSchema   ['type'] = new sfWidgetFormChoice(array('choices' => array('commission' => 'commission', 'question' => 'question', 'loi' => 'loi')));
    $this->validatorSchema['type'] = new sfValidatorChoice(array('choices' => array(0 => 'commission', 1 => 'question', 2 => 'loi'), 'required' => false));

    $this->widgetSchema   ['date'] = new sfWidgetFormDate();
    $this->validatorSchema['date'] = new sfValidatorDate(array('required' => false));

    $this->widgetSchema   ['personnalite_id'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Personnalite'), 'add_empty' => true));
    $this->validatorSchema['personnalite_id'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Personnalite'), 'required' => false));

    $this->widgetSchema   ['parlementaire_id'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Parlementaire'), 'add_empty' => true));
    $this->validatorSchema['parlementaire_id'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Parlementaire'), 'required' => false));

    $this->widgetSchema   ['fonction'] = new sfWidgetFormTextarea();
    $this->validatorSchema['fonction'] = new sfValidatorString(array('max_length' => 512, 'required' => false));

    $this->widgetSchema   ['created_at'] = new sfWidgetFormDateTime();
    $this->validatorSchema['created_at'] = new sfValidatorDateTime();

    $this->widgetSchema   ['updated_at'] = new sfWidgetFormDateTime();
    $this->validatorSchema['updated_at'] = new sfValidatorDateTime();

    $this->widgetSchema->setNameFormat('intervention[%s]');
  }

  public function getModelName()
  {
    return 'Intervention';
  }

}
