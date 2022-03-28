<?php

/**
 * Seance form base class.
 *
 * @method Seance getObject() Returns the current form's model object
 *
 * @package    senat
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedInheritanceTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseSeanceForm extends ObjectCommentableForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['date'] = new sfWidgetFormDate();
    $this->validatorSchema['date'] = new sfValidatorDate(array('required' => false));

    $this->widgetSchema   ['numero_semaine'] = new sfWidgetFormInputText();
    $this->validatorSchema['numero_semaine'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['annee'] = new sfWidgetFormInputText();
    $this->validatorSchema['annee'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['type'] = new sfWidgetFormChoice(array('choices' => array('commission' => 'commission', 'hemicycle' => 'hemicycle')));
    $this->validatorSchema['type'] = new sfValidatorChoice(array('choices' => array(0 => 'commission', 1 => 'hemicycle'), 'required' => false));

    $this->widgetSchema   ['moment'] = new sfWidgetFormInputText();
    $this->validatorSchema['moment'] = new sfValidatorString(array('max_length' => 30, 'required' => false));

    $this->widgetSchema   ['organisme_id'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Organisme'), 'add_empty' => true));
    $this->validatorSchema['organisme_id'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Organisme'), 'required' => false));

    $this->widgetSchema   ['tagged'] = new sfWidgetFormInputCheckbox();
    $this->validatorSchema['tagged'] = new sfValidatorBoolean(array('required' => false));

    $this->widgetSchema   ['session'] = new sfWidgetFormInputText();
    $this->validatorSchema['session'] = new sfValidatorString(array('max_length' => 10, 'required' => false));

    $this->widgetSchema   ['created_at'] = new sfWidgetFormDateTime();
    $this->validatorSchema['created_at'] = new sfValidatorDateTime();

    $this->widgetSchema   ['updated_at'] = new sfWidgetFormDateTime();
    $this->validatorSchema['updated_at'] = new sfValidatorDateTime();

    $this->widgetSchema->setNameFormat('seance[%s]');
  }

  public function getModelName()
  {
    return 'Seance';
  }

}
