<?php

/**
 * ParlementaireTexteloi form base class.
 *
 * @method ParlementaireTexteloi getObject() Returns the current form's model object
 *
 * @package    senat
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseParlementaireTexteloiForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'               => new sfWidgetFormInputHidden(),
      'parlementaire_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Parlementaire'), 'add_empty' => true)),
      'texteloi_id'      => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Texteloi'), 'add_empty' => true)),
      'importance'       => new sfWidgetFormInputText(),
      'fonction'         => new sfWidgetFormInputText(),
      'created_at'       => new sfWidgetFormDateTime(),
      'updated_at'       => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'               => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'parlementaire_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Parlementaire'), 'required' => false)),
      'texteloi_id'      => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Texteloi'), 'required' => false)),
      'importance'       => new sfValidatorInteger(array('required' => false)),
      'fonction'         => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'created_at'       => new sfValidatorDateTime(),
      'updated_at'       => new sfValidatorDateTime(),
    ));

    $this->widgetSchema->setNameFormat('parlementaire_texteloi[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'ParlementaireTexteloi';
  }

}
