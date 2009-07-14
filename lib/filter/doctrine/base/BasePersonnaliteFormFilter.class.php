<?php

require_once(sfConfig::get('sf_lib_dir').'/filter/doctrine/BaseFormFilterDoctrine.class.php');

/**
 * Personnalite filter form base class.
 *
 * @package    filters
 * @subpackage Personnalite *
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 11675 2008-09-19 15:21:38Z fabien $
 */
class BasePersonnaliteFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'nom'            => new sfWidgetFormFilterInput(),
      'nom_de_famille' => new sfWidgetFormFilterInput(),
      'sexe'           => new sfWidgetFormChoice(array('choices' => array('' => '', 'H' => 'H', 'F' => 'F'))),
      'created_at'     => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => true)),
      'updated_at'     => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => true)),
      'slug'           => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'nom'            => new sfValidatorPass(array('required' => false)),
      'nom_de_famille' => new sfValidatorPass(array('required' => false)),
      'sexe'           => new sfValidatorChoice(array('required' => false, 'choices' => array('H' => 'H', 'F' => 'F'))),
      'created_at'     => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'updated_at'     => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'slug'           => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('personnalite_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Personnalite';
  }

  public function getFields()
  {
    return array(
      'id'             => 'Number',
      'nom'            => 'Text',
      'nom_de_famille' => 'Text',
      'sexe'           => 'Enum',
      'created_at'     => 'Date',
      'updated_at'     => 'Date',
      'slug'           => 'Text',
    );
  }
}