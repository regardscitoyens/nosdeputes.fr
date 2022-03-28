<?php

/**
 * Presence form base class.
 *
 * @method Presence getObject() Returns the current form's model object
 *
 * @package    senat
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BasePresenceForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'               => new sfWidgetFormInputHidden(),
      'parlementaire_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Parlementaire'), 'add_empty' => true)),
      'seance_id'        => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Seance'), 'add_empty' => true)),
      'nb_preuves'       => new sfWidgetFormInputText(),
      'date'             => new sfWidgetFormDate(),
      'created_at'       => new sfWidgetFormDateTime(),
      'updated_at'       => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'               => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'parlementaire_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Parlementaire'), 'required' => false)),
      'seance_id'        => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Seance'), 'required' => false)),
      'nb_preuves'       => new sfValidatorInteger(array('required' => false)),
      'date'             => new sfValidatorDate(array('required' => false)),
      'created_at'       => new sfValidatorDateTime(),
      'updated_at'       => new sfValidatorDateTime(),
    ));

    $this->widgetSchema->setNameFormat('presence[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Presence';
  }

}
