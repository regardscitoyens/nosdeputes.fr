<?php

/**
 * Intervention filter form base class.
 *
 * @package    senat
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedInheritanceTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseInterventionFormFilter extends ObjectCommentableFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['nb_mots'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['nb_mots'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['md5'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['md5'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['intervention'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['intervention'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['timestamp'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['timestamp'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['source'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['source'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['seance_id'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Seance'), 'add_empty' => true));
    $this->validatorSchema['seance_id'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Seance'), 'column' => 'id'));

    $this->widgetSchema   ['section_id'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Section'), 'add_empty' => true));
    $this->validatorSchema['section_id'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Section'), 'column' => 'id'));

    $this->widgetSchema   ['type'] = new sfWidgetFormChoice(array('choices' => array('' => '', 'commission' => 'commission', 'question' => 'question', 'loi' => 'loi')));
    $this->validatorSchema['type'] = new sfValidatorChoice(array('required' => false, 'choices' => array('commission' => 'commission', 'question' => 'question', 'loi' => 'loi')));

    $this->widgetSchema   ['date'] = new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate()));
    $this->validatorSchema['date'] = new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDateTime(array('required' => false))));

    $this->widgetSchema   ['personnalite_id'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Personnalite'), 'add_empty' => true));
    $this->validatorSchema['personnalite_id'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Personnalite'), 'column' => 'id'));

    $this->widgetSchema   ['parlementaire_id'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Parlementaire'), 'add_empty' => true));
    $this->validatorSchema['parlementaire_id'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Parlementaire'), 'column' => 'id'));

    $this->widgetSchema   ['fonction'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['fonction'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['created_at'] = new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false));
    $this->validatorSchema['created_at'] = new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59'))));

    $this->widgetSchema   ['updated_at'] = new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false));
    $this->validatorSchema['updated_at'] = new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59'))));

    $this->widgetSchema->setNameFormat('intervention_filters[%s]');
  }

  public function getModelName()
  {
    return 'Intervention';
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
      'nb_mots' => 'Number',
      'md5' => 'Text',
      'intervention' => 'Text',
      'timestamp' => 'Number',
      'source' => 'Text',
      'seance_id' => 'ForeignKey',
      'section_id' => 'ForeignKey',
      'type' => 'Enum',
      'date' => 'Date',
      'personnalite_id' => 'ForeignKey',
      'parlementaire_id' => 'ForeignKey',
      'fonction' => 'Text',
      'created_at' => 'Date',
      'updated_at' => 'Date',
    ));
  }
}
