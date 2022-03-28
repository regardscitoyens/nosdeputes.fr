<?php

/**
 * ObjectCommentable filter form base class.
 *
 * @package    senat
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseObjectCommentableFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'nb_commentaires' => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'nb_commentaires' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('object_commentable_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'ObjectCommentable';
  }

  public function getFields()
  {
    return array(
      'id'              => 'Number',
      'nb_commentaires' => 'Number',
    );
  }
}
