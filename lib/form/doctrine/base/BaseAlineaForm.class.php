<?php

/**
 * Alinea form base class.
 *
 * @method Alinea getObject() Returns the current form's model object
 *
 * @package    senat
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedInheritanceTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseAlineaForm extends ObjectCommentableForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['texteloi_id'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Texteloi'), 'add_empty' => true));
    $this->validatorSchema['texteloi_id'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Texteloi'), 'required' => false));

    $this->widgetSchema   ['article_loi_id'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Article'), 'add_empty' => true));
    $this->validatorSchema['article_loi_id'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Article'), 'required' => false));

    $this->widgetSchema   ['numero'] = new sfWidgetFormInputText();
    $this->validatorSchema['numero'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['texte'] = new sfWidgetFormTextarea();
    $this->validatorSchema['texte'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['ref_loi'] = new sfWidgetFormInputText();
    $this->validatorSchema['ref_loi'] = new sfValidatorString(array('max_length' => 255, 'required' => false));

    $this->widgetSchema   ['created_at'] = new sfWidgetFormDateTime();
    $this->validatorSchema['created_at'] = new sfValidatorDateTime();

    $this->widgetSchema   ['updated_at'] = new sfWidgetFormDateTime();
    $this->validatorSchema['updated_at'] = new sfValidatorDateTime();

    $this->widgetSchema->setNameFormat('alinea[%s]');
  }

  public function getModelName()
  {
    return 'Alinea';
  }

}
