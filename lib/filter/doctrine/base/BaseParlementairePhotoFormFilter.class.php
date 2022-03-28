<?php

/**
 * ParlementairePhoto filter form base class.
 *
 * @package    senat
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseParlementairePhotoFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'slug'  => new sfWidgetFormFilterInput(),
      'photo' => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'slug'  => new sfValidatorPass(array('required' => false)),
      'photo' => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('parlementaire_photo_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'ParlementairePhoto';
  }

  public function getFields()
  {
    return array(
      'id'    => 'Number',
      'slug'  => 'Text',
      'photo' => 'Text',
    );
  }
}
