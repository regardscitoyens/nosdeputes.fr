<?php

/**
 * ParlementaireOrganisme filter form base class.
 *
 * @package    senat
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseParlementaireOrganismeFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'fonction'         => new sfWidgetFormFilterInput(),
      'importance'       => new sfWidgetFormFilterInput(),
      'debut_fonction'   => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
    ));

    $this->setValidators(array(
      'fonction'         => new sfValidatorPass(array('required' => false)),
      'importance'       => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'debut_fonction'   => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDateTime(array('required' => false)))),
    ));

    $this->widgetSchema->setNameFormat('parlementaire_organisme_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'ParlementaireOrganisme';
  }

  public function getFields()
  {
    return array(
      'fonction'         => 'Text',
      'importance'       => 'Number',
      'debut_fonction'   => 'Date',
      'organisme_id'     => 'Number',
      'parlementaire_id' => 'Number',
    );
  }
}
