<?php

/**
 * Seance filter form base class.
 *
 * @package    senat
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedInheritanceTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseSeanceFormFilter extends ObjectCommentableFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['date'] = new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate()));
    $this->validatorSchema['date'] = new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDateTime(array('required' => false))));

    $this->widgetSchema   ['numero_semaine'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['numero_semaine'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['annee'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['annee'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['type'] = new sfWidgetFormChoice(array('choices' => array('' => '', 'commission' => 'commission', 'hemicycle' => 'hemicycle')));
    $this->validatorSchema['type'] = new sfValidatorChoice(array('required' => false, 'choices' => array('commission' => 'commission', 'hemicycle' => 'hemicycle')));

    $this->widgetSchema   ['moment'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['moment'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['organisme_id'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Organisme'), 'add_empty' => true));
    $this->validatorSchema['organisme_id'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Organisme'), 'column' => 'id'));

    $this->widgetSchema   ['tagged'] = new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no')));
    $this->validatorSchema['tagged'] = new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0)));

    $this->widgetSchema   ['session'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['session'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['created_at'] = new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false));
    $this->validatorSchema['created_at'] = new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59'))));

    $this->widgetSchema   ['updated_at'] = new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false));
    $this->validatorSchema['updated_at'] = new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59'))));

    $this->widgetSchema->setNameFormat('seance_filters[%s]');
  }

  public function getModelName()
  {
    return 'Seance';
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
      'date' => 'Date',
      'numero_semaine' => 'Number',
      'annee' => 'Number',
      'type' => 'Enum',
      'moment' => 'Text',
      'organisme_id' => 'ForeignKey',
      'tagged' => 'Boolean',
      'session' => 'Text',
      'created_at' => 'Date',
      'updated_at' => 'Date',
    ));
  }
}
