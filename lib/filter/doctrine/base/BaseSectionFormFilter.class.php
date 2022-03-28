<?php

/**
 * Section filter form base class.
 *
 * @package    senat
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedInheritanceTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseSectionFormFilter extends ObjectCommentableFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['md5'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['md5'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['titre'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['titre'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['titre_complet'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['titre_complet'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['section_id'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Section'), 'add_empty' => true));
    $this->validatorSchema['section_id'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Section'), 'column' => 'id'));

    $this->widgetSchema   ['min_date'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['min_date'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['max_date'] = new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate()));
    $this->validatorSchema['max_date'] = new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDateTime(array('required' => false))));

    $this->widgetSchema   ['timestamp'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['timestamp'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['nb_interventions'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['nb_interventions'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['id_dossier_institution'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['id_dossier_institution'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['created_at'] = new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false));
    $this->validatorSchema['created_at'] = new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59'))));

    $this->widgetSchema   ['updated_at'] = new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false));
    $this->validatorSchema['updated_at'] = new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59'))));

    $this->widgetSchema->setNameFormat('section_filters[%s]');
  }

  public function getModelName()
  {
    return 'Section';
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
      'md5' => 'Text',
      'titre' => 'Text',
      'titre_complet' => 'Text',
      'section_id' => 'ForeignKey',
      'min_date' => 'Text',
      'max_date' => 'Date',
      'timestamp' => 'Number',
      'nb_interventions' => 'Number',
      'id_dossier_institution' => 'Text',
      'created_at' => 'Date',
      'updated_at' => 'Date',
    ));
  }
}
