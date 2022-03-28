<?php

/**
 * Personnalite filter form base class.
 *
 * @package    senat
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedInheritanceTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BasePersonnaliteFormFilter extends ObjectCommentableFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['nom'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['nom'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['nom_de_famille'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['nom_de_famille'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['sexe'] = new sfWidgetFormChoice(array('choices' => array('' => '', 'H' => 'H', 'F' => 'F')));
    $this->validatorSchema['sexe'] = new sfValidatorChoice(array('required' => false, 'choices' => array('H' => 'H', 'F' => 'F')));

    $this->widgetSchema   ['date_naissance'] = new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate()));
    $this->validatorSchema['date_naissance'] = new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDateTime(array('required' => false))));

    $this->widgetSchema   ['created_at'] = new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false));
    $this->validatorSchema['created_at'] = new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59'))));

    $this->widgetSchema   ['updated_at'] = new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false));
    $this->validatorSchema['updated_at'] = new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59'))));

    $this->widgetSchema   ['slug'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['slug'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema->setNameFormat('personnalite_filters[%s]');
  }

  public function getModelName()
  {
    return 'Personnalite';
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
      'nom' => 'Text',
      'nom_de_famille' => 'Text',
      'sexe' => 'Enum',
      'date_naissance' => 'Date',
      'created_at' => 'Date',
      'updated_at' => 'Date',
      'slug' => 'Text',
    ));
  }
}
