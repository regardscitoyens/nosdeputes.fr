<?php

/**
 * PreuvePresence filter form base class.
 *
 * @package    senat
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BasePreuvePresenceFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'presence_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Presence'), 'add_empty' => true)),
      'type'        => new sfWidgetFormChoice(array('choices' => array('' => '', 'jo' => 'jo', 'intervention' => 'intervention', 'compte-rendu' => 'compte-rendu', 'autre' => 'autre'))),
      'source'      => new sfWidgetFormFilterInput(),
      'created_at'  => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at'  => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
    ));

    $this->setValidators(array(
      'presence_id' => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Presence'), 'column' => 'id')),
      'type'        => new sfValidatorChoice(array('required' => false, 'choices' => array('jo' => 'jo', 'intervention' => 'intervention', 'compte-rendu' => 'compte-rendu', 'autre' => 'autre'))),
      'source'      => new sfValidatorPass(array('required' => false)),
      'created_at'  => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at'  => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
    ));

    $this->widgetSchema->setNameFormat('preuve_presence_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'PreuvePresence';
  }

  public function getFields()
  {
    return array(
      'id'          => 'Number',
      'presence_id' => 'ForeignKey',
      'type'        => 'Enum',
      'source'      => 'Text',
      'created_at'  => 'Date',
      'updated_at'  => 'Date',
    );
  }
}
