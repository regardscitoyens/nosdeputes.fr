<?php

/**
 * Personnalite form base class.
 *
 * @package    form
 * @subpackage personnalite
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 8508 2008-04-17 17:39:15Z fabien $
 */
class BasePersonnaliteForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'             => new sfWidgetFormInputHidden(),
      'nom'            => new sfWidgetFormInput(),
      'nom_de_famille' => new sfWidgetFormInput(),
      'sexe'           => new sfWidgetFormChoice(array('choices' => array('H' => 'H', 'F' => 'F'))),
      'created_at'     => new sfWidgetFormDateTime(),
      'updated_at'     => new sfWidgetFormDateTime(),
      'slug'           => new sfWidgetFormInput(),
    ));

    $this->setValidators(array(
      'id'             => new sfValidatorDoctrineChoice(array('model' => 'Personnalite', 'column' => 'id', 'required' => false)),
      'nom'            => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'nom_de_famille' => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'sexe'           => new sfValidatorChoice(array('choices' => array('H' => 'H', 'F' => 'F'), 'required' => false)),
      'created_at'     => new sfValidatorDateTime(array('required' => false)),
      'updated_at'     => new sfValidatorDateTime(array('required' => false)),
      'slug'           => new sfValidatorString(array('max_length' => 255, 'required' => false)),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'Personnalite', 'column' => array('slug')))
    );

    $this->widgetSchema->setNameFormat('personnalite[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Personnalite';
  }

}
