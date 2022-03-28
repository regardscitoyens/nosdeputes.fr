<?php

/**
 * ParlementairePhoto form base class.
 *
 * @method ParlementairePhoto getObject() Returns the current form's model object
 *
 * @package    senat
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseParlementairePhotoForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'    => new sfWidgetFormInputHidden(),
      'slug'  => new sfWidgetFormInputText(),
      'photo' => new sfWidgetFormTextarea(),
    ));

    $this->setValidators(array(
      'id'    => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'slug'  => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'photo' => new sfValidatorString(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('parlementaire_photo[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'ParlementairePhoto';
  }

}
