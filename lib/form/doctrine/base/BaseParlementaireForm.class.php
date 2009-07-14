<?php

/**
 * Parlementaire form base class.
 *
 * @package    form
 * @subpackage parlementaire
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 8508 2008-04-17 17:39:15Z fabien $
 */
class BaseParlementaireForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'              => new sfWidgetFormInputHidden(),
      'nom'             => new sfWidgetFormInput(),
      'nom_de_famille'  => new sfWidgetFormInput(),
      'sexe'            => new sfWidgetFormChoice(array('choices' => array('H' => 'H', 'F' => 'F'))),
      'nom_circo'       => new sfWidgetFormInput(),
      'num_circo'       => new sfWidgetFormInput(),
      'site_web'        => new sfWidgetFormInput(),
      'debut_mandat'    => new sfWidgetFormDate(),
      'place_hemicycle' => new sfWidgetFormInput(),
      'url_an'          => new sfWidgetFormInput(),
      'profession'      => new sfWidgetFormInput(),
      'id_an'           => new sfWidgetFormInput(),
      'type'            => new sfWidgetFormChoice(array('choices' => array('depute' => 'depute', 'senateur' => 'senateur'))),
      'created_at'      => new sfWidgetFormDateTime(),
      'updated_at'      => new sfWidgetFormDateTime(),
      'slug'            => new sfWidgetFormInput(),
      'organismes_list' => new sfWidgetFormDoctrineChoiceMany(array('model' => 'Organisme')),
    ));

    $this->setValidators(array(
      'id'              => new sfValidatorDoctrineChoice(array('model' => 'Parlementaire', 'column' => 'id', 'required' => false)),
      'nom'             => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'nom_de_famille'  => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'sexe'            => new sfValidatorChoice(array('choices' => array('H' => 'H', 'F' => 'F'), 'required' => false)),
      'nom_circo'       => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'num_circo'       => new sfValidatorInteger(array('required' => false)),
      'site_web'        => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'debut_mandat'    => new sfValidatorDate(array('required' => false)),
      'place_hemicycle' => new sfValidatorInteger(array('required' => false)),
      'url_an'          => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'profession'      => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'id_an'           => new sfValidatorInteger(array('required' => false)),
      'type'            => new sfValidatorChoice(array('choices' => array('depute' => 'depute', 'senateur' => 'senateur'), 'required' => false)),
      'created_at'      => new sfValidatorDateTime(array('required' => false)),
      'updated_at'      => new sfValidatorDateTime(array('required' => false)),
      'slug'            => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'organismes_list' => new sfValidatorDoctrineChoiceMany(array('model' => 'Organisme', 'required' => false)),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorAnd(array(
        new sfValidatorDoctrineUnique(array('model' => 'Parlementaire', 'column' => array('id_an'))),
        new sfValidatorDoctrineUnique(array('model' => 'Parlementaire', 'column' => array('slug'))),
      ))
    );

    $this->widgetSchema->setNameFormat('parlementaire[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Parlementaire';
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
