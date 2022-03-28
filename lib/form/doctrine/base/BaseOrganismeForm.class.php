<?php

/**
 * Organisme form base class.
 *
 * @method Organisme getObject() Returns the current form's model object
 *
 * @package    senat
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseOrganismeForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                  => new sfWidgetFormInputHidden(),
      'nom'                 => new sfWidgetFormTextarea(),
      'type'                => new sfWidgetFormChoice(array('choices' => array('parlementaire' => 'parlementaire', 'groupe' => 'groupe', 'extra' => 'extra', 'groupes' => 'groupes'))),
      'created_at'          => new sfWidgetFormDateTime(),
      'updated_at'          => new sfWidgetFormDateTime(),
      'slug'                => new sfWidgetFormInputText(),
      'parlementaires_list' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'Parlementaire')),
    ));

    $this->setValidators(array(
      'id'                  => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'nom'                 => new sfValidatorString(array('max_length' => 512, 'required' => false)),
      'type'                => new sfValidatorChoice(array('choices' => array(0 => 'parlementaire', 1 => 'groupe', 2 => 'extra', 3 => 'groupes'), 'required' => false)),
      'created_at'          => new sfValidatorDateTime(),
      'updated_at'          => new sfValidatorDateTime(),
      'slug'                => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'parlementaires_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'Parlementaire', 'required' => false)),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'Organisme', 'column' => array('slug')))
    );

    $this->widgetSchema->setNameFormat('organisme[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Organisme';
  }

  public function updateDefaultsFromObject()
  {
    parent::updateDefaultsFromObject();

    if (isset($this->widgetSchema['parlementaires_list']))
    {
      $this->setDefault('parlementaires_list', $this->object->Parlementaires->getPrimaryKeys());
    }

  }

  protected function doSave($con = null)
  {
    $this->saveParlementairesList($con);

    parent::doSave($con);
  }

  public function saveParlementairesList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['parlementaires_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->Parlementaires->getPrimaryKeys();
    $values = $this->getValue('parlementaires_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('Parlementaires', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('Parlementaires', array_values($link));
    }
  }

}
