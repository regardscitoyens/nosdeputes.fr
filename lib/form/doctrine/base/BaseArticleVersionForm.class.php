<?php

/**
 * ArticleVersion form base class.
 *
 * @method ArticleVersion getObject() Returns the current form's model object
 *
 * @package    senat
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseArticleVersionForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'              => new sfWidgetFormInputHidden(),
      'nb_commentaires' => new sfWidgetFormInputText(),
      'titre'           => new sfWidgetFormInputText(),
      'corps'           => new sfWidgetFormTextarea(),
      'user_corps'      => new sfWidgetFormTextarea(),
      'categorie'       => new sfWidgetFormInputText(),
      'citoyen_id'      => new sfWidgetFormInputText(),
      'article_id'      => new sfWidgetFormInputText(),
      'link'            => new sfWidgetFormInputText(),
      'status'          => new sfWidgetFormChoice(array('choices' => array('public' => 'public', 'brouillon' => 'brouillon', 'offline' => 'offline'))),
      'object_id'       => new sfWidgetFormInputText(),
      'created_at'      => new sfWidgetFormDateTime(),
      'updated_at'      => new sfWidgetFormDateTime(),
      'version'         => new sfWidgetFormInputHidden(),
    ));

    $this->setValidators(array(
      'id'              => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'nb_commentaires' => new sfValidatorInteger(array('required' => false)),
      'titre'           => new sfValidatorString(array('max_length' => 254, 'required' => false)),
      'corps'           => new sfValidatorString(array('required' => false)),
      'user_corps'      => new sfValidatorString(array('required' => false)),
      'categorie'       => new sfValidatorString(array('max_length' => 128, 'required' => false)),
      'citoyen_id'      => new sfValidatorInteger(array('required' => false)),
      'article_id'      => new sfValidatorInteger(array('required' => false)),
      'link'            => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'status'          => new sfValidatorChoice(array('choices' => array(0 => 'public', 1 => 'brouillon', 2 => 'offline'), 'required' => false)),
      'object_id'       => new sfValidatorInteger(array('required' => false)),
      'created_at'      => new sfValidatorDateTime(),
      'updated_at'      => new sfValidatorDateTime(),
      'version'         => new sfValidatorChoice(array('choices' => array($this->getObject()->get('version')), 'empty_value' => $this->getObject()->get('version'), 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('article_version[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'ArticleVersion';
  }

}
