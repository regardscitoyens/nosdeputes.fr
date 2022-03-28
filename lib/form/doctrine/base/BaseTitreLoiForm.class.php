<?php

/**
 * TitreLoi form base class.
 *
 * @method TitreLoi getObject() Returns the current form's model object
 *
 * @package    senat
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedInheritanceTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseTitreLoiForm extends ObjectCommentableForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['texteloi_id'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Texteloi'), 'add_empty' => true));
    $this->validatorSchema['texteloi_id'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Texteloi'), 'required' => false));

    $this->widgetSchema   ['chapitre'] = new sfWidgetFormInputText();
    $this->validatorSchema['chapitre'] = new sfValidatorString(array('max_length' => 8, 'required' => false));

    $this->widgetSchema   ['section'] = new sfWidgetFormInputText();
    $this->validatorSchema['section'] = new sfValidatorString(array('max_length' => 8, 'required' => false));

    $this->widgetSchema   ['titre'] = new sfWidgetFormTextarea();
    $this->validatorSchema['titre'] = new sfValidatorString(array('max_length' => 512, 'required' => false));

    $this->widgetSchema   ['expose'] = new sfWidgetFormTextarea();
    $this->validatorSchema['expose'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['parlementaire_id'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Parlementaire'), 'add_empty' => true));
    $this->validatorSchema['parlementaire_id'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Parlementaire'), 'required' => false));

    $this->widgetSchema   ['date'] = new sfWidgetFormDate();
    $this->validatorSchema['date'] = new sfValidatorDate(array('required' => false));

    $this->widgetSchema   ['source'] = new sfWidgetFormInputText();
    $this->validatorSchema['source'] = new sfValidatorString(array('max_length' => 128, 'required' => false));

    $this->widgetSchema   ['nb_articles'] = new sfWidgetFormInputText();
    $this->validatorSchema['nb_articles'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['titre_loi_id'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('TitreLoi'), 'add_empty' => true));
    $this->validatorSchema['titre_loi_id'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('TitreLoi'), 'required' => false));

    $this->widgetSchema   ['created_at'] = new sfWidgetFormDateTime();
    $this->validatorSchema['created_at'] = new sfValidatorDateTime();

    $this->widgetSchema   ['updated_at'] = new sfWidgetFormDateTime();
    $this->validatorSchema['updated_at'] = new sfValidatorDateTime();

    $this->widgetSchema->setNameFormat('titre_loi[%s]');
  }

  public function getModelName()
  {
    return 'TitreLoi';
  }

}
