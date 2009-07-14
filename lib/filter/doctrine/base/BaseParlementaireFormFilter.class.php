<?php

require_once(sfConfig::get('sf_lib_dir').'/filter/doctrine/BaseFormFilterDoctrine.class.php');

/**
 * Parlementaire filter form base class.
 *
 * @package    filters
 * @subpackage Parlementaire *
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 11675 2008-09-19 15:21:38Z fabien $
 */
class BaseParlementaireFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'nom'             => new sfWidgetFormFilterInput(),
      'nom_de_famille'  => new sfWidgetFormFilterInput(),
      'sexe'            => new sfWidgetFormChoice(array('choices' => array('' => '', 'H' => 'H', 'F' => 'F'))),
      'nom_circo'       => new sfWidgetFormFilterInput(),
      'num_circo'       => new sfWidgetFormFilterInput(),
      'site_web'        => new sfWidgetFormFilterInput(),
      'debut_mandat'    => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => true)),
      'place_hemicycle' => new sfWidgetFormFilterInput(),
      'url_an'          => new sfWidgetFormFilterInput(),
      'profession'      => new sfWidgetFormFilterInput(),
      'id_an'           => new sfWidgetFormFilterInput(),
      'type'            => new sfWidgetFormChoice(array('choices' => array('' => '', 'depute' => 'depute', 'senateur' => 'senateur'))),
      'created_at'      => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => true)),
      'updated_at'      => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => true)),
      'slug'            => new sfWidgetFormFilterInput(),
      'organismes_list' => new sfWidgetFormDoctrineChoiceMany(array('model' => 'Organisme')),
    ));

    $this->setValidators(array(
      'nom'             => new sfValidatorPass(array('required' => false)),
      'nom_de_famille'  => new sfValidatorPass(array('required' => false)),
      'sexe'            => new sfValidatorChoice(array('required' => false, 'choices' => array('H' => 'H', 'F' => 'F'))),
      'nom_circo'       => new sfValidatorPass(array('required' => false)),
      'num_circo'       => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'site_web'        => new sfValidatorPass(array('required' => false)),
      'debut_mandat'    => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'place_hemicycle' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'url_an'          => new sfValidatorPass(array('required' => false)),
      'profession'      => new sfValidatorPass(array('required' => false)),
      'id_an'           => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'type'            => new sfValidatorChoice(array('required' => false, 'choices' => array('depute' => 'depute', 'senateur' => 'senateur'))),
      'created_at'      => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'updated_at'      => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'slug'            => new sfValidatorPass(array('required' => false)),
      'organismes_list' => new sfValidatorDoctrineChoiceMany(array('model' => 'Organisme', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('parlementaire_filters[%s]');

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
          ->andWhereIn('ParlementaireOrganisme.organisme_id', $values);
  }

  public function getModelName()
  {
    return 'Parlementaire';
  }

  public function getFields()
  {
    return array(
      'id'              => 'Number',
      'nom'             => 'Text',
      'nom_de_famille'  => 'Text',
      'sexe'            => 'Enum',
      'nom_circo'       => 'Text',
      'num_circo'       => 'Number',
      'site_web'        => 'Text',
      'debut_mandat'    => 'Date',
      'place_hemicycle' => 'Number',
      'url_an'          => 'Text',
      'profession'      => 'Text',
      'id_an'           => 'Number',
      'type'            => 'Enum',
      'created_at'      => 'Date',
      'updated_at'      => 'Date',
      'slug'            => 'Text',
      'organismes_list' => 'ManyKey',
    );
  }
}