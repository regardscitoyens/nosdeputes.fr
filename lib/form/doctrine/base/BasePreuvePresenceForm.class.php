<?php

/**
 * PreuvePresence form base class.
 *
 * @method PreuvePresence getObject() Returns the current form's model object
 *
 * @package    senat
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BasePreuvePresenceForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'          => new sfWidgetFormInputHidden(),
      'presence_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Presence'), 'add_empty' => true)),
      'type'        => new sfWidgetFormChoice(array('choices' => array('jo' => 'jo', 'intervention' => 'intervention', 'compte-rendu' => 'compte-rendu', 'autre' => 'autre'))),
      'source'      => new sfWidgetFormInputText(),
      'created_at'  => new sfWidgetFormDateTime(),
      'updated_at'  => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'          => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'presence_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Presence'), 'required' => false)),
      'type'        => new sfValidatorChoice(array('choices' => array(0 => 'jo', 1 => 'intervention', 2 => 'compte-rendu', 3 => 'autre'), 'required' => false)),
      'source'      => new sfValidatorString(array('max_length' => 200, 'required' => false)),
      'created_at'  => new sfValidatorDateTime(),
      'updated_at'  => new sfValidatorDateTime(),
    ));

    $this->widgetSchema->setNameFormat('preuve_presence[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'PreuvePresence';
  }

}
