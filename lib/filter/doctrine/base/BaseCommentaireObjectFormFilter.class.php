<?php

/**
 * CommentaireObject filter form base class.
 *
 * @package    senat
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseCommentaireObjectFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'object_type'    => new sfWidgetFormFilterInput(),
      'object_id'      => new sfWidgetFormFilterInput(),
      'commentaire_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Commentaire'), 'add_empty' => true)),
    ));

    $this->setValidators(array(
      'object_type'    => new sfValidatorPass(array('required' => false)),
      'object_id'      => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'commentaire_id' => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Commentaire'), 'column' => 'id')),
    ));

    $this->widgetSchema->setNameFormat('commentaire_object_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'CommentaireObject';
  }

  public function getFields()
  {
    return array(
      'id'             => 'Number',
      'object_type'    => 'Text',
      'object_id'      => 'Number',
      'commentaire_id' => 'ForeignKey',
    );
  }
}
