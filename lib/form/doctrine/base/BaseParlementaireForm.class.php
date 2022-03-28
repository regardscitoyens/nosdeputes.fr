<?php

/**
 * Parlementaire form base class.
 *
 * @method Parlementaire getObject() Returns the current form's model object
 *
 * @package    senat
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedInheritanceTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseParlementaireForm extends PersonnaliteForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['nom_circo'] = new sfWidgetFormInputText();
    $this->validatorSchema['nom_circo'] = new sfValidatorString(array('max_length' => 255, 'required' => false));

    $this->widgetSchema   ['num_circo'] = new sfWidgetFormInputText();
    $this->validatorSchema['num_circo'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['sites_web'] = new sfWidgetFormTextarea();
    $this->validatorSchema['sites_web'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['debut_mandat'] = new sfWidgetFormDate();
    $this->validatorSchema['debut_mandat'] = new sfValidatorDate(array('required' => false));

    $this->widgetSchema   ['fin_mandat'] = new sfWidgetFormDate();
    $this->validatorSchema['fin_mandat'] = new sfValidatorDate(array('required' => false));

    $this->widgetSchema   ['place_hemicycle'] = new sfWidgetFormInputText();
    $this->validatorSchema['place_hemicycle'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['url_institution'] = new sfWidgetFormInputText();
    $this->validatorSchema['url_institution'] = new sfValidatorString(array('max_length' => 255, 'required' => false));

    $this->widgetSchema   ['profession'] = new sfWidgetFormInputText();
    $this->validatorSchema['profession'] = new sfValidatorString(array('max_length' => 255, 'required' => false));

    $this->widgetSchema   ['autoflip'] = new sfWidgetFormInputCheckbox();
    $this->validatorSchema['autoflip'] = new sfValidatorBoolean(array('required' => false));

    $this->widgetSchema   ['id_institution'] = new sfWidgetFormInputText();
    $this->validatorSchema['id_institution'] = new sfValidatorString(array('max_length' => 64, 'required' => false));

    $this->widgetSchema   ['type'] = new sfWidgetFormChoice(array('choices' => array('depute' => 'depute', 'senateur' => 'senateur')));
    $this->validatorSchema['type'] = new sfValidatorChoice(array('choices' => array(0 => 'depute', 1 => 'senateur'), 'required' => false));

    $this->widgetSchema   ['groupe_acronyme'] = new sfWidgetFormInputText();
    $this->validatorSchema['groupe_acronyme'] = new sfValidatorString(array('max_length' => 8, 'required' => false));

    $this->widgetSchema   ['parti'] = new sfWidgetFormInputText();
    $this->validatorSchema['parti'] = new sfValidatorString(array('max_length' => 255, 'required' => false));

    $this->widgetSchema   ['adresses'] = new sfWidgetFormTextarea();
    $this->validatorSchema['adresses'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['suppleant_de_id'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('SuppleantDe'), 'add_empty' => true));
    $this->validatorSchema['suppleant_de_id'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('SuppleantDe'), 'required' => false));

    $this->widgetSchema   ['anciens_mandats'] = new sfWidgetFormTextarea();
    $this->validatorSchema['anciens_mandats'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['autres_mandats'] = new sfWidgetFormTextarea();
    $this->validatorSchema['autres_mandats'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['mails'] = new sfWidgetFormTextarea();
    $this->validatorSchema['mails'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['top'] = new sfWidgetFormTextarea();
    $this->validatorSchema['top'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['villes'] = new sfWidgetFormTextarea();
    $this->validatorSchema['villes'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['organismes_list'] = new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'Organisme'));
    $this->validatorSchema['organismes_list'] = new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'Organisme', 'required' => false));

    $this->widgetSchema   ['amendements_list'] = new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'Amendement'));
    $this->validatorSchema['amendements_list'] = new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'Amendement', 'required' => false));

    $this->widgetSchema   ['textelois_list'] = new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'Texteloi'));
    $this->validatorSchema['textelois_list'] = new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'Texteloi', 'required' => false));

    $this->widgetSchema->setNameFormat('parlementaire[%s]');
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

    if (isset($this->widgetSchema['amendements_list']))
    {
      $this->setDefault('amendements_list', $this->object->Amendements->getPrimaryKeys());
    }

    if (isset($this->widgetSchema['textelois_list']))
    {
      $this->setDefault('textelois_list', $this->object->Textelois->getPrimaryKeys());
    }

  }

  protected function doSave($con = null)
  {
    $this->saveOrganismesList($con);
    $this->saveAmendementsList($con);
    $this->saveTexteloisList($con);

    parent::doSave($con);
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

    if (null === $con)
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

  public function saveAmendementsList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['amendements_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->Amendements->getPrimaryKeys();
    $values = $this->getValue('amendements_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('Amendements', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('Amendements', array_values($link));
    }
  }

  public function saveTexteloisList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['textelois_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->Textelois->getPrimaryKeys();
    $values = $this->getValue('textelois_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('Textelois', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('Textelois', array_values($link));
    }
  }

}
