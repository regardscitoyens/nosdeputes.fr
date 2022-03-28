<?php

/**
 * Alinea filter form base class.
 *
 * @package    senat
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedInheritanceTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseAlineaFormFilter extends ObjectCommentableFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['texteloi_id'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Texteloi'), 'add_empty' => true));
    $this->validatorSchema['texteloi_id'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Texteloi'), 'column' => 'updated_at'));

    $this->widgetSchema   ['article_loi_id'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Article'), 'add_empty' => true));
    $this->validatorSchema['article_loi_id'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Article'), 'column' => 'id'));

    $this->widgetSchema   ['numero'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['numero'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['texte'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['texte'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['ref_loi'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['ref_loi'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['created_at'] = new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false));
    $this->validatorSchema['created_at'] = new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59'))));

    $this->widgetSchema   ['updated_at'] = new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false));
    $this->validatorSchema['updated_at'] = new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59'))));

    $this->widgetSchema->setNameFormat('alinea_filters[%s]');
  }

  public function getModelName()
  {
    return 'Alinea';
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
      'texteloi_id' => 'ForeignKey',
      'article_loi_id' => 'ForeignKey',
      'numero' => 'Number',
      'texte' => 'Text',
      'ref_loi' => 'Text',
      'created_at' => 'Date',
      'updated_at' => 'Date',
    ));
  }
}
