<?php

/**
 * Commentaire filter form base class.
 *
 * @package    senat
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedInheritanceTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseCommentaireFormFilter extends ObjectRatedFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['citoyen_id'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Citoyen'), 'add_empty' => true));
    $this->validatorSchema['citoyen_id'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Citoyen'), 'column' => 'id'));

    $this->widgetSchema   ['commentaire'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['commentaire'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['is_public'] = new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no')));
    $this->validatorSchema['is_public'] = new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0)));

    $this->widgetSchema   ['ip_address'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['ip_address'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['object_type'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['object_type'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['object_id'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['object_id'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['lien'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['lien'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['presentation'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['presentation'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['created_at'] = new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false));
    $this->validatorSchema['created_at'] = new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59'))));

    $this->widgetSchema   ['updated_at'] = new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false));
    $this->validatorSchema['updated_at'] = new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59'))));

    $this->widgetSchema->setNameFormat('commentaire_filters[%s]');
  }

  public function getModelName()
  {
    return 'Commentaire';
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
      'citoyen_id' => 'ForeignKey',
      'commentaire' => 'Text',
      'is_public' => 'Boolean',
      'ip_address' => 'Text',
      'object_type' => 'Text',
      'object_id' => 'Text',
      'lien' => 'Text',
      'presentation' => 'Text',
      'created_at' => 'Date',
      'updated_at' => 'Date',
    ));
  }
}
