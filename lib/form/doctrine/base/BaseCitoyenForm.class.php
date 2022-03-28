<?php

/**
 * Citoyen form base class.
 *
 * @method Citoyen getObject() Returns the current form's model object
 *
 * @package    senat
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseCitoyenForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                  => new sfWidgetFormInputHidden(),
      'login'               => new sfWidgetFormInputText(),
      'password'            => new sfWidgetFormInputText(),
      'email'               => new sfWidgetFormInputText(),
      'activite'            => new sfWidgetFormInputText(),
      'url_site'            => new sfWidgetFormInputText(),
      'employe_institution' => new sfWidgetFormInputCheckbox(),
      'travail_pour'        => new sfWidgetFormInputText(),
      'naissance'           => new sfWidgetFormDate(),
      'sexe'                => new sfWidgetFormChoice(array('choices' => array('' => NULL, 'H' => 'H', 'F' => 'F'))),
      'nom_circo'           => new sfWidgetFormInputText(),
      'num_circo'           => new sfWidgetFormInputText(),
      'photo'               => new sfWidgetFormTextarea(),
      'activation_id'       => new sfWidgetFormInputText(),
      'is_active'           => new sfWidgetFormInputCheckbox(),
      'role'                => new sfWidgetFormChoice(array('choices' => array('membre' => 'membre', 'moderateur' => 'moderateur', 'admin' => 'admin'))),
      'last_login'          => new sfWidgetFormDateTime(),
      'parametres'          => new sfWidgetFormTextarea(),
      'created_at'          => new sfWidgetFormDateTime(),
      'updated_at'          => new sfWidgetFormDateTime(),
      'slug'                => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'id'                  => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'login'               => new sfValidatorString(array('max_length' => 128, 'required' => false)),
      'password'            => new sfValidatorString(array('max_length' => 40, 'required' => false)),
      'email'               => new sfValidatorString(array('max_length' => 255)),
      'activite'            => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'url_site'            => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'employe_institution' => new sfValidatorBoolean(array('required' => false)),
      'travail_pour'        => new sfValidatorInteger(array('required' => false)),
      'naissance'           => new sfValidatorDate(array('required' => false)),
      'sexe'                => new sfValidatorChoice(array('choices' => array(0 => NULL, 1 => 'H', 2 => 'F'), 'required' => false)),
      'nom_circo'           => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'num_circo'           => new sfValidatorInteger(array('required' => false)),
      'photo'               => new sfValidatorString(array('required' => false)),
      'activation_id'       => new sfValidatorString(array('max_length' => 32, 'required' => false)),
      'is_active'           => new sfValidatorBoolean(array('required' => false)),
      'role'                => new sfValidatorChoice(array('choices' => array(0 => 'membre', 1 => 'moderateur', 2 => 'admin'), 'required' => false)),
      'last_login'          => new sfValidatorDateTime(array('required' => false)),
      'parametres'          => new sfValidatorString(array('required' => false)),
      'created_at'          => new sfValidatorDateTime(),
      'updated_at'          => new sfValidatorDateTime(),
      'slug'                => new sfValidatorString(array('max_length' => 255, 'required' => false)),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorAnd(array(
        new sfValidatorDoctrineUnique(array('model' => 'Citoyen', 'column' => array('login'))),
        new sfValidatorDoctrineUnique(array('model' => 'Citoyen', 'column' => array('email'))),
        new sfValidatorDoctrineUnique(array('model' => 'Citoyen', 'column' => array('slug'))),
      ))
    );

    $this->widgetSchema->setNameFormat('citoyen[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Citoyen';
  }

}
