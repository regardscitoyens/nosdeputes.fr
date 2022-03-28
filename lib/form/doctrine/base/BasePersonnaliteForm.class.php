<?php

/**
 * Personnalite form base class.
 *
 * @method Personnalite getObject() Returns the current form's model object
 *
 * @package    senat
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedInheritanceTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BasePersonnaliteForm extends ObjectCommentableForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['nom'] = new sfWidgetFormInputText();
    $this->validatorSchema['nom'] = new sfValidatorString(array('max_length' => 255, 'required' => false));

    $this->widgetSchema   ['nom_de_famille'] = new sfWidgetFormInputText();
    $this->validatorSchema['nom_de_famille'] = new sfValidatorString(array('max_length' => 255, 'required' => false));

    $this->widgetSchema   ['sexe'] = new sfWidgetFormChoice(array('choices' => array('H' => 'H', 'F' => 'F')));
    $this->validatorSchema['sexe'] = new sfValidatorChoice(array('choices' => array(0 => 'H', 1 => 'F'), 'required' => false));

    $this->widgetSchema   ['date_naissance'] = new sfWidgetFormDate();
    $this->validatorSchema['date_naissance'] = new sfValidatorDate(array('required' => false));

    $this->widgetSchema   ['created_at'] = new sfWidgetFormDateTime();
    $this->validatorSchema['created_at'] = new sfValidatorDateTime();

    $this->widgetSchema   ['updated_at'] = new sfWidgetFormDateTime();
    $this->validatorSchema['updated_at'] = new sfValidatorDateTime();

    $this->widgetSchema   ['slug'] = new sfWidgetFormInputText();
    $this->validatorSchema['slug'] = new sfValidatorString(array('max_length' => 255, 'required' => false));

    $this->widgetSchema->setNameFormat('personnalite[%s]');
  }

  public function getModelName()
  {
    return 'Personnalite';
  }

}
