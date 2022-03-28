<?php

/**
 * Alerte filter form base class.
 *
 * @package    senat
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseAlerteFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'email'          => new sfWidgetFormFilterInput(),
      'query'          => new sfWidgetFormFilterInput(),
      'filter'         => new sfWidgetFormFilterInput(),
      'query_md5'      => new sfWidgetFormFilterInput(),
      'titre'          => new sfWidgetFormFilterInput(),
      'confirmed'      => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'no_human_query' => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'period'         => new sfWidgetFormChoice(array('choices' => array('' => '', 'HOUR' => 'HOUR', 'DAY' => 'DAY', 'WEEK' => 'WEEK', 'MONTH' => 'MONTH'))),
      'next_mail'      => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'last_mail'      => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'citoyen_id'     => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Citoyen'), 'add_empty' => true)),
      'verif'          => new sfWidgetFormFilterInput(),
      'created_at'     => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at'     => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
    ));

    $this->setValidators(array(
      'email'          => new sfValidatorPass(array('required' => false)),
      'query'          => new sfValidatorPass(array('required' => false)),
      'filter'         => new sfValidatorPass(array('required' => false)),
      'query_md5'      => new sfValidatorPass(array('required' => false)),
      'titre'          => new sfValidatorPass(array('required' => false)),
      'confirmed'      => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'no_human_query' => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'period'         => new sfValidatorChoice(array('required' => false, 'choices' => array('HOUR' => 'HOUR', 'DAY' => 'DAY', 'WEEK' => 'WEEK', 'MONTH' => 'MONTH'))),
      'next_mail'      => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'last_mail'      => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'citoyen_id'     => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Citoyen'), 'column' => 'id')),
      'verif'          => new sfValidatorPass(array('required' => false)),
      'created_at'     => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at'     => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
    ));

    $this->widgetSchema->setNameFormat('alerte_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Alerte';
  }

  public function getFields()
  {
    return array(
      'id'             => 'Number',
      'email'          => 'Text',
      'query'          => 'Text',
      'filter'         => 'Text',
      'query_md5'      => 'Text',
      'titre'          => 'Text',
      'confirmed'      => 'Boolean',
      'no_human_query' => 'Boolean',
      'period'         => 'Enum',
      'next_mail'      => 'Date',
      'last_mail'      => 'Date',
      'citoyen_id'     => 'ForeignKey',
      'verif'          => 'Text',
      'created_at'     => 'Date',
      'updated_at'     => 'Date',
    );
  }
}
