<?php

require_once(sfConfig::get('sf_lib_dir').'/filter/doctrine/BaseFormFilterDoctrine.class.php');

/**
 * ParlementaireOrganisme filter form base class.
 *
 * @package    filters
 * @subpackage ParlementaireOrganisme *
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 11675 2008-09-19 15:21:38Z fabien $
 */
class BaseParlementaireOrganismeFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'fonction'         => new sfWidgetFormFilterInput(),
      'debut_fonction'   => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => true)),
    ));

    $this->setValidators(array(
      'fonction'         => new sfValidatorPass(array('required' => false)),
      'debut_fonction'   => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
    ));

    $this->widgetSchema->setNameFormat('parlementaire_organisme_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

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
      'debut_fonction'   => 'Date',
      'organisme_id'     => 'Number',
      'parlementaire_id' => 'Number',
    );
  }
}