<?php

/**
 * ParlementaireTexteloi filter form base class.
 *
 * @package    senat
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseParlementaireTexteloiFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'parlementaire_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Parlementaire'), 'add_empty' => true)),
      'texteloi_id'      => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Texteloi'), 'add_empty' => true)),
      'importance'       => new sfWidgetFormFilterInput(),
      'fonction'         => new sfWidgetFormFilterInput(),
      'created_at'       => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at'       => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
    ));

    $this->setValidators(array(
      'parlementaire_id' => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Parlementaire'), 'column' => 'id')),
      'texteloi_id'      => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Texteloi'), 'column' => 'updated_at')),
      'importance'       => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'fonction'         => new sfValidatorPass(array('required' => false)),
      'created_at'       => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at'       => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
    ));

    $this->widgetSchema->setNameFormat('parlementaire_texteloi_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'ParlementaireTexteloi';
  }

  public function getFields()
  {
    return array(
      'id'               => 'Number',
      'parlementaire_id' => 'ForeignKey',
      'texteloi_id'      => 'ForeignKey',
      'importance'       => 'Number',
      'fonction'         => 'Text',
      'created_at'       => 'Date',
      'updated_at'       => 'Date',
    );
  }
}
