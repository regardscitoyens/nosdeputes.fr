<?php

/**
 * Alerte form base class.
 *
 * @method Alerte getObject() Returns the current form's model object
 *
 * @package    senat
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseAlerteForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'             => new sfWidgetFormInputHidden(),
      'email'          => new sfWidgetFormInputText(),
      'query'          => new sfWidgetFormTextarea(),
      'filter'         => new sfWidgetFormTextarea(),
      'query_md5'      => new sfWidgetFormInputText(),
      'titre'          => new sfWidgetFormTextarea(),
      'confirmed'      => new sfWidgetFormInputCheckbox(),
      'no_human_query' => new sfWidgetFormInputCheckbox(),
      'period'         => new sfWidgetFormChoice(array('choices' => array('HOUR' => 'HOUR', 'DAY' => 'DAY', 'WEEK' => 'WEEK', 'MONTH' => 'MONTH'))),
      'next_mail'      => new sfWidgetFormInputText(),
      'last_mail'      => new sfWidgetFormInputText(),
      'citoyen_id'     => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Citoyen'), 'add_empty' => true)),
      'verif'          => new sfWidgetFormInputText(),
      'created_at'     => new sfWidgetFormDateTime(),
      'updated_at'     => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'             => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'email'          => new sfValidatorString(array('max_length' => 128, 'required' => false)),
      'query'          => new sfValidatorString(array('required' => false)),
      'filter'         => new sfValidatorString(array('required' => false)),
      'query_md5'      => new sfValidatorString(array('max_length' => 32, 'required' => false)),
      'titre'          => new sfValidatorString(array('required' => false)),
      'confirmed'      => new sfValidatorBoolean(array('required' => false)),
      'no_human_query' => new sfValidatorBoolean(array('required' => false)),
      'period'         => new sfValidatorChoice(array('choices' => array(0 => 'HOUR', 1 => 'DAY', 2 => 'WEEK', 3 => 'MONTH'), 'required' => false)),
      'next_mail'      => new sfValidatorPass(array('required' => false)),
      'last_mail'      => new sfValidatorPass(array('required' => false)),
      'citoyen_id'     => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Citoyen'), 'required' => false)),
      'verif'          => new sfValidatorString(array('max_length' => 128, 'required' => false)),
      'created_at'     => new sfValidatorDateTime(),
      'updated_at'     => new sfValidatorDateTime(),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'Alerte', 'column' => array('email', 'citoyen_id', 'query_md5')))
    );

    $this->widgetSchema->setNameFormat('alerte[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Alerte';
  }

}
