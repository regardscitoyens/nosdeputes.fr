<?php

/**
 * Article form base class.
 *
 * @method Article getObject() Returns the current form's model object
 *
 * @package    senat
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedInheritanceTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseArticleForm extends ObjectCommentableForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['titre'] = new sfWidgetFormInputText();
    $this->validatorSchema['titre'] = new sfValidatorString(array('max_length' => 254, 'required' => false));

    $this->widgetSchema   ['corps'] = new sfWidgetFormTextarea();
    $this->validatorSchema['corps'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['user_corps'] = new sfWidgetFormTextarea();
    $this->validatorSchema['user_corps'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['categorie'] = new sfWidgetFormInputText();
    $this->validatorSchema['categorie'] = new sfValidatorString(array('max_length' => 128, 'required' => false));

    $this->widgetSchema   ['citoyen_id'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Citoyen'), 'add_empty' => true));
    $this->validatorSchema['citoyen_id'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Citoyen'), 'required' => false));

    $this->widgetSchema   ['article_id'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Article'), 'add_empty' => true));
    $this->validatorSchema['article_id'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Article'), 'required' => false));

    $this->widgetSchema   ['link'] = new sfWidgetFormInputText();
    $this->validatorSchema['link'] = new sfValidatorString(array('max_length' => 255, 'required' => false));

    $this->widgetSchema   ['status'] = new sfWidgetFormChoice(array('choices' => array('public' => 'public', 'brouillon' => 'brouillon', 'offline' => 'offline')));
    $this->validatorSchema['status'] = new sfValidatorChoice(array('choices' => array(0 => 'public', 1 => 'brouillon', 2 => 'offline'), 'required' => false));

    $this->widgetSchema   ['object_id'] = new sfWidgetFormInputText();
    $this->validatorSchema['object_id'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['created_at'] = new sfWidgetFormDateTime();
    $this->validatorSchema['created_at'] = new sfValidatorDateTime();

    $this->widgetSchema   ['updated_at'] = new sfWidgetFormDateTime();
    $this->validatorSchema['updated_at'] = new sfValidatorDateTime();

    $this->widgetSchema   ['version'] = new sfWidgetFormInputText();
    $this->validatorSchema['version'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['slug'] = new sfWidgetFormInputText();
    $this->validatorSchema['slug'] = new sfValidatorString(array('max_length' => 255, 'required' => false));

    $this->widgetSchema->setNameFormat('article[%s]');
  }

  public function getModelName()
  {
    return 'Article';
  }

}
