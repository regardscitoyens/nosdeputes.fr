<?php

/**
 * CommentaireObject form base class.
 *
 * @method CommentaireObject getObject() Returns the current form's model object
 *
 * @package    senat
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseCommentaireObjectForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'             => new sfWidgetFormInputHidden(),
      'object_type'    => new sfWidgetFormInputText(),
      'object_id'      => new sfWidgetFormInputText(),
      'commentaire_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Commentaire'), 'add_empty' => true)),
    ));

    $this->setValidators(array(
      'id'             => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'object_type'    => new sfValidatorString(array('max_length' => 64, 'required' => false)),
      'object_id'      => new sfValidatorInteger(array('required' => false)),
      'commentaire_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Commentaire'), 'required' => false)),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'CommentaireObject', 'column' => array('object_type', 'object_id', 'commentaire_id')))
    );

    $this->widgetSchema->setNameFormat('commentaire_object[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'CommentaireObject';
  }

}
