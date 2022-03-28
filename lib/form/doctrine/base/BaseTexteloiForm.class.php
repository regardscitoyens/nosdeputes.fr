<?php

/**
 * Texteloi form base class.
 *
 * @method Texteloi getObject() Returns the current form's model object
 *
 * @package    senat
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedInheritanceTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseTexteloiForm extends ObjectCommentableForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['numero'] = new sfWidgetFormInputText();
    $this->validatorSchema['numero'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['annexe'] = new sfWidgetFormInputText();
    $this->validatorSchema['annexe'] = new sfValidatorString(array('max_length' => 12, 'required' => false));

    $this->widgetSchema   ['type'] = new sfWidgetFormChoice(array('choices' => array('Motion' => 'Motion', 'Proposition de loi' => 'Proposition de loi', 'Proposition de résolution' => 'Proposition de résolution', 'Projet de loi' => 'Projet de loi', 'Texte adopté' => 'Texte adopté', 'Texte de la commission' => 'Texte de la commission', 'Texte de la commission mixte paritaire' => 'Texte de la commission mixte paritaire', 'Lettre' => 'Lettre', 'Rapport' => 'Rapport', 'Rapport d\'information' => 'Rapport d\'information', 'Rapport d\'office parlementaire' => 'Rapport d\'office parlementaire', 'Avis' => 'Avis', 'Rapport de groupe d\'amitié' => 'Rapport de groupe d\'amitié', 'Rapport de commission d\'enquête' => 'Rapport de commission d\'enquête')));
    $this->validatorSchema['type'] = new sfValidatorChoice(array('choices' => array(0 => 'Motion', 1 => 'Proposition de loi', 2 => 'Proposition de résolution', 3 => 'Projet de loi', 4 => 'Texte adopté', 5 => 'Texte de la commission', 6 => 'Texte de la commission mixte paritaire', 7 => 'Lettre', 8 => 'Rapport', 9 => 'Rapport d\'information', 10 => 'Rapport d\'office parlementaire', 11 => 'Avis', 12 => 'Rapport de groupe d\'amitié', 13 => 'Rapport de commission d\'enquête'), 'required' => false));

    $this->widgetSchema   ['type_details'] = new sfWidgetFormTextarea();
    $this->validatorSchema['type_details'] = new sfValidatorString(array('max_length' => 512, 'required' => false));

    $this->widgetSchema   ['categorie'] = new sfWidgetFormInputText();
    $this->validatorSchema['categorie'] = new sfValidatorString(array('max_length' => 128, 'required' => false));

    $this->widgetSchema   ['id_dossier_institution'] = new sfWidgetFormInputText();
    $this->validatorSchema['id_dossier_institution'] = new sfValidatorString(array('max_length' => 255, 'required' => false));

    $this->widgetSchema   ['titre'] = new sfWidgetFormTextarea();
    $this->validatorSchema['titre'] = new sfValidatorString(array('max_length' => 512, 'required' => false));

    $this->widgetSchema   ['date'] = new sfWidgetFormDate();
    $this->validatorSchema['date'] = new sfValidatorDate(array('required' => false));

    $this->widgetSchema   ['source'] = new sfWidgetFormInputText();
    $this->validatorSchema['source'] = new sfValidatorString(array('max_length' => 128, 'required' => false));

    $this->widgetSchema   ['organisme_id'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Organisme'), 'add_empty' => true));
    $this->validatorSchema['organisme_id'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Organisme'), 'required' => false));

    $this->widgetSchema   ['signataires'] = new sfWidgetFormTextarea();
    $this->validatorSchema['signataires'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['contenu'] = new sfWidgetFormTextarea();
    $this->validatorSchema['contenu'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['created_at'] = new sfWidgetFormDateTime();
    $this->validatorSchema['created_at'] = new sfValidatorDateTime();

    $this->widgetSchema   ['updated_at'] = new sfWidgetFormDateTime();
    $this->validatorSchema['updated_at'] = new sfValidatorDateTime();

    $this->widgetSchema   ['parlementaires_list'] = new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'Parlementaire'));
    $this->validatorSchema['parlementaires_list'] = new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'Parlementaire', 'required' => false));

    $this->widgetSchema->setNameFormat('texteloi[%s]');
  }

  public function getModelName()
  {
    return 'Texteloi';
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
