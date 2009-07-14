<?php

/**
 * Organisme form base class.
 *
 * @package    form
 * @subpackage organisme
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 8508 2008-04-17 17:39:15Z fabien $
 */
class BaseOrganismeForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'              => new sfWidgetFormInputHidden(),
      'nom'             => new sfWidgetFormInput(),
      'type'            => new sfWidgetFormChoice(array('choices' => array('parlementaire' => 'parlementaire', 'groupe' => 'groupe', 'extra' => 'extra'))),
      'created_at'      => new sfWidgetFormDateTime(),
      'updated_at'      => new sfWidgetFormDateTime(),
      'slug'            => new sfWidgetFormInput(),
      'organismes_list' => new sfWidgetFormDoctrineChoiceMany(array('model' => 'Parlementaire')),
    ));

    $this->setValidators(array(
      'id'              => new sfValidatorDoctrineChoice(array('model' => 'Organisme', 'column' => 'id', 'required' => false)),
      'nom'             => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'type'            => new sfValidatorChoice(array('choices' => array('parlementaire' => 'parlementaire', 'groupe' => 'groupe', 'extra' => 'extra'), 'required' => false)),
      'created_at'      => new sfValidatorDateTime(array('required' => false)),
      'updated_at'      => new sfValidatorDateTime(array('required' => false)),
      'slug'            => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'organismes_list' => new sfValidatorDoctrineChoiceMany(array('model' => 'Parlementaire', 'required' => false)),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorAnd(array(
        new sfValidatorDoctrineUnique(array('model' => 'Organisme', 'column' => array('nom'))),
        new sfValidatorDoctrineUnique(array('model' => 'Organisme', 'column' => array('slug'))),
      ))
    );

    $this->widgetSchema->setNameFormat('organisme[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Organisme';
  }

  public function updateDefaultsFromObject()
  {
    parent::updateDefaultsFromObject();

    if (isset($this->widgetSchema['organismes_list']))
    {
      $this->setDefault('organismes_list', $this->object->Organismes->getPrimaryKeys());
    }

  }

  protected function doSave($con = null)
  {
    parent::doSave($con);

    $this->saveOrganismesList($con);
  }

  public function saveOrganismesList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['organismes_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (is_null($con))
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->Organismes->getPrimaryKeys();
    $values = $this->getValue('organismes_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('Organismes', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('Organismes', array_values($link));
    }
  }

}
