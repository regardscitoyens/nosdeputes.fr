<?php

/**
 * ParlementaireOrganisme form base class.
 *
 * @method ParlementaireOrganisme getObject() Returns the current form's model object
 *
 * @package    senat
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseParlementaireOrganismeForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'fonction'         => new sfWidgetFormTextarea(),
      'importance'       => new sfWidgetFormInputText(),
      'debut_fonction'   => new sfWidgetFormDate(),
      'organisme_id'     => new sfWidgetFormInputHidden(),
      'parlementaire_id' => new sfWidgetFormInputHidden(),
    ));

    $this->setValidators(array(
      'fonction'         => new sfValidatorString(array('max_length' => 512, 'required' => false)),
      'importance'       => new sfValidatorInteger(array('required' => false)),
      'debut_fonction'   => new sfValidatorDate(array('required' => false)),
      'organisme_id'     => new sfValidatorChoice(array('choices' => array($this->getObject()->get('organisme_id')), 'empty_value' => $this->getObject()->get('organisme_id'), 'required' => false)),
      'parlementaire_id' => new sfValidatorChoice(array('choices' => array($this->getObject()->get('parlementaire_id')), 'empty_value' => $this->getObject()->get('parlementaire_id'), 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('parlementaire_organisme[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'ParlementaireOrganisme';
  }

}
