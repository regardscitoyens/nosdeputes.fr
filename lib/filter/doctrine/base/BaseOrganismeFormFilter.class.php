<?php

require_once(sfConfig::get('sf_lib_dir').'/filter/doctrine/BaseFormFilterDoctrine.class.php');

/**
 * Organisme filter form base class.
 *
 * @package    filters
 * @subpackage Organisme *
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 11675 2008-09-19 15:21:38Z fabien $
 */
class BaseOrganismeFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'nom'             => new sfWidgetFormFilterInput(),
      'type'            => new sfWidgetFormChoice(array('choices' => array('' => '', 'parlementaire' => 'parlementaire', 'groupe' => 'groupe', 'extra' => 'extra'))),
      'created_at'      => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => true)),
      'updated_at'      => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => true)),
      'slug'            => new sfWidgetFormFilterInput(),
      'organismes_list' => new sfWidgetFormDoctrineChoiceMany(array('model' => 'Parlementaire')),
    ));

    $this->setValidators(array(
      'nom'             => new sfValidatorPass(array('required' => false)),
      'type'            => new sfValidatorChoice(array('required' => false, 'choices' => array('parlementaire' => 'parlementaire', 'groupe' => 'groupe', 'extra' => 'extra'))),
      'created_at'      => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'updated_at'      => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'slug'            => new sfValidatorPass(array('required' => false)),
      'organismes_list' => new sfValidatorDoctrineChoiceMany(array('model' => 'Parlementaire', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('organisme_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function addOrganismesListColumnQuery(Doctrine_Query $query, $field, $values)
  {
    if (!is_array($values))
    {
      $values = array($values);
    }

    if (!count($values))
    {
      return;
    }

    $query->leftJoin('r.ParlementaireOrganisme ParlementaireOrganisme')
          ->andWhereIn('ParlementaireOrganisme.parlementaire_id', $values);
  }

  public function getModelName()
  {
    return 'Organisme';
  }

  public function getFields()
  {
    return array(
      'id'              => 'Number',
      'nom'             => 'Text',
      'type'            => 'Enum',
      'created_at'      => 'Date',
      'updated_at'      => 'Date',
      'slug'            => 'Text',
      'organismes_list' => 'ManyKey',
    );
  }
}