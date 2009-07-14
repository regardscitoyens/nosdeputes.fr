<?php

/**
 * ParlementaireOrganisme form base class.
 *
 * @package    form
 * @subpackage parlementaire_organisme
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 8508 2008-04-17 17:39:15Z fabien $
 */
class BaseParlementaireOrganismeForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'fonction'         => new sfWidgetFormInput(),
      'debut_fonction'   => new sfWidgetFormDate(),
      'organisme_id'     => new sfWidgetFormInputHidden(),
      'parlementaire_id' => new sfWidgetFormInputHidden(),
    ));

    $this->setValidators(array(
      'fonction'         => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'debut_fonction'   => new sfValidatorDate(array('required' => false)),
      'organisme_id'     => new sfValidatorDoctrineChoice(array('model' => 'ParlementaireOrganisme', 'column' => 'organisme_id', 'required' => false)),
      'parlementaire_id' => new sfValidatorDoctrineChoice(array('model' => 'ParlementaireOrganisme', 'column' => 'parlementaire_id', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('parlementaire_organisme[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ParlementaireOrganisme';
  }

}
