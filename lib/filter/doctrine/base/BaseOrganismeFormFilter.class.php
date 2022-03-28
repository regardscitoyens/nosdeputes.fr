<?php

/**
 * Organisme filter form base class.
 *
 * @package    senat
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseOrganismeFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'nom'                 => new sfWidgetFormFilterInput(),
      'type'                => new sfWidgetFormChoice(array('choices' => array('' => '', 'parlementaire' => 'parlementaire', 'groupe' => 'groupe', 'extra' => 'extra', 'groupes' => 'groupes'))),
      'created_at'          => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at'          => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'slug'                => new sfWidgetFormFilterInput(),
      'parlementaires_list' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'Parlementaire')),
    ));

    $this->setValidators(array(
      'nom'                 => new sfValidatorPass(array('required' => false)),
      'type'                => new sfValidatorChoice(array('required' => false, 'choices' => array('parlementaire' => 'parlementaire', 'groupe' => 'groupe', 'extra' => 'extra', 'groupes' => 'groupes'))),
      'created_at'          => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at'          => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'slug'                => new sfValidatorPass(array('required' => false)),
      'parlementaires_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'Parlementaire', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('organisme_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function addParlementairesListColumnQuery(Doctrine_Query $query, $field, $values)
  {
    if (!is_array($values))
    {
      $values = array($values);
    }

    if (!count($values))
    {
      return;
    }

    $query
      ->leftJoin($query->getRootAlias().'.ParlementaireOrganisme ParlementaireOrganisme')
      ->andWhereIn('ParlementaireOrganisme.parlementaire_id', $values)
    ;
  }

  public function getModelName()
  {
    return 'Organisme';
  }

  public function getFields()
  {
    return array(
      'id'                  => 'Number',
      'nom'                 => 'Text',
      'type'                => 'Enum',
      'created_at'          => 'Date',
      'updated_at'          => 'Date',
      'slug'                => 'Text',
      'parlementaires_list' => 'ManyKey',
    );
  }
}
