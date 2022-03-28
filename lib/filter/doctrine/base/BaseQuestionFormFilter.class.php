<?php

/**
 * Question filter form base class.
 *
 * @package    senat
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedInheritanceTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseQuestionFormFilter extends ObjectCommentableFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['source'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['source'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['legislature'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['legislature'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['type'] = new sfWidgetFormChoice(array('choices' => array('' => '', 'Question écrite' => 'Question écrite', 'Question d\'actualité au gouvernement' => 'Question d\'actualité au gouvernement', 'Question crible thématique' => 'Question crible thématique', 'Question orale sans débat' => 'Question orale sans débat', 'Question orale avec débat' => 'Question orale avec débat', 'Question orale avec débat portant sur un sujet européen' => 'Question orale avec débat portant sur un sujet européen')));
    $this->validatorSchema['type'] = new sfValidatorChoice(array('required' => false, 'choices' => array('Question écrite' => 'Question écrite', 'Question d\'actualité au gouvernement' => 'Question d\'actualité au gouvernement', 'Question crible thématique' => 'Question crible thématique', 'Question orale sans débat' => 'Question orale sans débat', 'Question orale avec débat' => 'Question orale avec débat', 'Question orale avec débat portant sur un sujet européen' => 'Question orale avec débat portant sur un sujet européen')));

    $this->widgetSchema   ['numero'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['numero'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['date'] = new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate()));
    $this->validatorSchema['date'] = new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDateTime(array('required' => false))));

    $this->widgetSchema   ['date_cloture'] = new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate()));
    $this->validatorSchema['date_cloture'] = new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDateTime(array('required' => false))));

    $this->widgetSchema   ['ministere'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['ministere'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['titre'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['titre'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['question'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['question'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['reponse'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['reponse'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['motif_retrait'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['motif_retrait'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['content_md5'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['content_md5'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['parlementaire_id'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Parlementaire'), 'add_empty' => true));
    $this->validatorSchema['parlementaire_id'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Parlementaire'), 'column' => 'id'));

    $this->widgetSchema   ['created_at'] = new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false));
    $this->validatorSchema['created_at'] = new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59'))));

    $this->widgetSchema   ['updated_at'] = new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false));
    $this->validatorSchema['updated_at'] = new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59'))));

    $this->widgetSchema->setNameFormat('question_filters[%s]');
  }

  public function getModelName()
  {
    return 'Question';
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
      'source' => 'Text',
      'legislature' => 'Number',
      'type' => 'Enum',
      'numero' => 'Text',
      'date' => 'Date',
      'date_cloture' => 'Date',
      'ministere' => 'Text',
      'titre' => 'Text',
      'question' => 'Text',
      'reponse' => 'Text',
      'motif_retrait' => 'Text',
      'content_md5' => 'Text',
      'parlementaire_id' => 'ForeignKey',
      'created_at' => 'Date',
      'updated_at' => 'Date',
    ));
  }
}
