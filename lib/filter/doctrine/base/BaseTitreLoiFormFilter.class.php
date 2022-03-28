<?php

/**
 * TitreLoi filter form base class.
 *
 * @package    senat
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedInheritanceTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseTitreLoiFormFilter extends ObjectCommentableFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['texteloi_id'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Texteloi'), 'add_empty' => true));
    $this->validatorSchema['texteloi_id'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Texteloi'), 'column' => 'updated_at'));

    $this->widgetSchema   ['chapitre'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['chapitre'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['section'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['section'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['titre'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['titre'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['expose'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['expose'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['parlementaire_id'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Parlementaire'), 'add_empty' => true));
    $this->validatorSchema['parlementaire_id'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Parlementaire'), 'column' => 'id'));

    $this->widgetSchema   ['date'] = new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate()));
    $this->validatorSchema['date'] = new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDateTime(array('required' => false))));

    $this->widgetSchema   ['source'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['source'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['nb_articles'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['nb_articles'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['titre_loi_id'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('TitreLoi'), 'add_empty' => true));
    $this->validatorSchema['titre_loi_id'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('TitreLoi'), 'column' => 'id'));

    $this->widgetSchema   ['created_at'] = new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false));
    $this->validatorSchema['created_at'] = new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59'))));

    $this->widgetSchema   ['updated_at'] = new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false));
    $this->validatorSchema['updated_at'] = new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59'))));

    $this->widgetSchema->setNameFormat('titre_loi_filters[%s]');
  }

  public function getModelName()
  {
    return 'TitreLoi';
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
      'texteloi_id' => 'ForeignKey',
      'chapitre' => 'Text',
      'section' => 'Text',
      'titre' => 'Text',
      'expose' => 'Text',
      'parlementaire_id' => 'ForeignKey',
      'date' => 'Date',
      'source' => 'Text',
      'nb_articles' => 'Number',
      'titre_loi_id' => 'ForeignKey',
      'created_at' => 'Date',
      'updated_at' => 'Date',
    ));
  }
}
