<?php

/**
 * Question form base class.
 *
 * @method Question getObject() Returns the current form's model object
 *
 * @package    senat
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedInheritanceTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseQuestionForm extends ObjectCommentableForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['source'] = new sfWidgetFormInputText();
    $this->validatorSchema['source'] = new sfValidatorString(array('max_length' => 255, 'required' => false));

    $this->widgetSchema   ['legislature'] = new sfWidgetFormInputText();
    $this->validatorSchema['legislature'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['type'] = new sfWidgetFormChoice(array('choices' => array('Question écrite' => 'Question écrite', 'Question d\'actualité au gouvernement' => 'Question d\'actualité au gouvernement', 'Question crible thématique' => 'Question crible thématique', 'Question orale sans débat' => 'Question orale sans débat', 'Question orale avec débat' => 'Question orale avec débat', 'Question orale avec débat portant sur un sujet européen' => 'Question orale avec débat portant sur un sujet européen')));
    $this->validatorSchema['type'] = new sfValidatorChoice(array('choices' => array(0 => 'Question écrite', 1 => 'Question d\'actualité au gouvernement', 2 => 'Question crible thématique', 3 => 'Question orale sans débat', 4 => 'Question orale avec débat', 5 => 'Question orale avec débat portant sur un sujet européen'), 'required' => false));

    $this->widgetSchema   ['numero'] = new sfWidgetFormInputText();
    $this->validatorSchema['numero'] = new sfValidatorString(array('max_length' => 8, 'required' => false));

    $this->widgetSchema   ['date'] = new sfWidgetFormDate();
    $this->validatorSchema['date'] = new sfValidatorDate(array('required' => false));

    $this->widgetSchema   ['date_cloture'] = new sfWidgetFormDate();
    $this->validatorSchema['date_cloture'] = new sfValidatorDate(array('required' => false));

    $this->widgetSchema   ['ministere'] = new sfWidgetFormTextarea();
    $this->validatorSchema['ministere'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['titre'] = new sfWidgetFormTextarea();
    $this->validatorSchema['titre'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['question'] = new sfWidgetFormTextarea();
    $this->validatorSchema['question'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['reponse'] = new sfWidgetFormTextarea();
    $this->validatorSchema['reponse'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['motif_retrait'] = new sfWidgetFormTextarea();
    $this->validatorSchema['motif_retrait'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['content_md5'] = new sfWidgetFormInputText();
    $this->validatorSchema['content_md5'] = new sfValidatorString(array('max_length' => 36, 'required' => false));

    $this->widgetSchema   ['parlementaire_id'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Parlementaire'), 'add_empty' => true));
    $this->validatorSchema['parlementaire_id'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Parlementaire'), 'required' => false));

    $this->widgetSchema   ['created_at'] = new sfWidgetFormDateTime();
    $this->validatorSchema['created_at'] = new sfValidatorDateTime();

    $this->widgetSchema   ['updated_at'] = new sfWidgetFormDateTime();
    $this->validatorSchema['updated_at'] = new sfValidatorDateTime();

    $this->widgetSchema->setNameFormat('question[%s]');
  }

  public function getModelName()
  {
    return 'Question';
  }

}
