<?php

/**
 * ArticleLoi form base class.
 *
 * @method ArticleLoi getObject() Returns the current form's model object
 *
 * @package    senat
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedInheritanceTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseArticleLoiForm extends ObjectCommentableForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['texteloi_id'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Texteloi'), 'add_empty' => true));
    $this->validatorSchema['texteloi_id'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Texteloi'), 'required' => false));

    $this->widgetSchema   ['titre'] = new sfWidgetFormInputText();
    $this->validatorSchema['titre'] = new sfValidatorString(array('max_length' => 20, 'required' => false));

    $this->widgetSchema   ['ordre'] = new sfWidgetFormInputText();
    $this->validatorSchema['ordre'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['precedent'] = new sfWidgetFormInputText();
    $this->validatorSchema['precedent'] = new sfValidatorString(array('max_length' => 20, 'required' => false));

    $this->widgetSchema   ['suivant'] = new sfWidgetFormInputText();
    $this->validatorSchema['suivant'] = new sfValidatorString(array('max_length' => 20, 'required' => false));

    $this->widgetSchema   ['expose'] = new sfWidgetFormTextarea();
    $this->validatorSchema['expose'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['titre_loi_id'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('TitreLoi'), 'add_empty' => true));
    $this->validatorSchema['titre_loi_id'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('TitreLoi'), 'required' => false));

    $this->widgetSchema   ['slug'] = new sfWidgetFormInputText();
    $this->validatorSchema['slug'] = new sfValidatorString(array('max_length' => 255, 'required' => false));

    $this->widgetSchema->setNameFormat('article_loi[%s]');
  }

  public function getModelName()
  {
    return 'ArticleLoi';
  }

}
