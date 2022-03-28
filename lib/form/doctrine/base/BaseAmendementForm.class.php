<?php

/**
 * Amendement form base class.
 *
 * @method Amendement getObject() Returns the current form's model object
 *
 * @package    senat
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedInheritanceTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseAmendementForm extends ObjectCommentableForm
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['source'] = new sfWidgetFormInputText();
    $this->validatorSchema['source'] = new sfValidatorString(array('max_length' => 255, 'required' => false));

    $this->widgetSchema   ['texteloi_id'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Texteloi'), 'add_empty' => true));
    $this->validatorSchema['texteloi_id'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Texteloi'), 'required' => false));

    $this->widgetSchema   ['numero'] = new sfWidgetFormInputText();
    $this->validatorSchema['numero'] = new sfValidatorString(array('max_length' => 8, 'required' => false));

    $this->widgetSchema   ['rectif'] = new sfWidgetFormInputText();
    $this->validatorSchema['rectif'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['sujet'] = new sfWidgetFormInputText();
    $this->validatorSchema['sujet'] = new sfValidatorString(array('max_length' => 100, 'required' => false));

    $this->widgetSchema   ['sort'] = new sfWidgetFormChoice(array('choices' => array('Indéfini' => 'Indéfini', 'Adopté' => 'Adopté', 'Irrecevable' => 'Irrecevable', 'Rejeté' => 'Rejeté', 'Retiré' => 'Retiré', 'Tombe' => 'Tombe', 'Non soutenu' => 'Non soutenu', 'Retiré avant séance' => 'Retiré avant séance', 'Rectifié' => 'Rectifié', 'Satisfait' => 'Satisfait')));
    $this->validatorSchema['sort'] = new sfValidatorChoice(array('choices' => array(0 => 'Indéfini', 1 => 'Adopté', 2 => 'Irrecevable', 3 => 'Rejeté', 4 => 'Retiré', 5 => 'Tombe', 6 => 'Non soutenu', 7 => 'Retiré avant séance', 8 => 'Rectifié', 9 => 'Satisfait'), 'required' => false));

    $this->widgetSchema   ['avis_comm'] = new sfWidgetFormChoice(array('choices' => array('Indéfini' => 'Indéfini', 'Favorable' => 'Favorable', 'Défavorable' => 'Défavorable', 'Demande de retrait' => 'Demande de retrait', 'Sagesse' => 'Sagesse')));
    $this->validatorSchema['avis_comm'] = new sfValidatorChoice(array('choices' => array(0 => 'Indéfini', 1 => 'Favorable', 2 => 'Défavorable', 3 => 'Demande de retrait', 4 => 'Sagesse'), 'required' => false));

    $this->widgetSchema   ['avis_gouv'] = new sfWidgetFormChoice(array('choices' => array('Indéfini' => 'Indéfini', 'Favorable' => 'Favorable', 'Défavorable' => 'Défavorable', 'Demande de retrait' => 'Demande de retrait', 'Sagesse' => 'Sagesse')));
    $this->validatorSchema['avis_gouv'] = new sfValidatorChoice(array('choices' => array(0 => 'Indéfini', 1 => 'Favorable', 2 => 'Défavorable', 3 => 'Demande de retrait', 4 => 'Sagesse'), 'required' => false));

    $this->widgetSchema   ['date'] = new sfWidgetFormDate();
    $this->validatorSchema['date'] = new sfValidatorDate(array('required' => false));

    $this->widgetSchema   ['signataires'] = new sfWidgetFormTextarea();
    $this->validatorSchema['signataires'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['texte'] = new sfWidgetFormTextarea();
    $this->validatorSchema['texte'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['expose'] = new sfWidgetFormTextarea();
    $this->validatorSchema['expose'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema   ['ref_loi'] = new sfWidgetFormInputText();
    $this->validatorSchema['ref_loi'] = new sfValidatorString(array('max_length' => 255, 'required' => false));

    $this->widgetSchema   ['organisme_id'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Commission'), 'add_empty' => true));
    $this->validatorSchema['organisme_id'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Commission'), 'required' => false));

    $this->widgetSchema   ['numero_pere'] = new sfWidgetFormInputText();
    $this->validatorSchema['numero_pere'] = new sfValidatorInteger(array('required' => false));

    $this->widgetSchema   ['content_md5'] = new sfWidgetFormInputText();
    $this->validatorSchema['content_md5'] = new sfValidatorString(array('max_length' => 36, 'required' => false));

    $this->widgetSchema   ['created_at'] = new sfWidgetFormDateTime();
    $this->validatorSchema['created_at'] = new sfValidatorDateTime();

    $this->widgetSchema   ['updated_at'] = new sfWidgetFormDateTime();
    $this->validatorSchema['updated_at'] = new sfValidatorDateTime();

    $this->widgetSchema   ['parlementaires_list'] = new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'Parlementaire'));
    $this->validatorSchema['parlementaires_list'] = new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'Parlementaire', 'required' => false));

    $this->widgetSchema->setNameFormat('amendement[%s]');
  }

  public function getModelName()
  {
    return 'Amendement';
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
