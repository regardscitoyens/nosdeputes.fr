<?php

/**
 * Article filter form base class.
 *
 * @package    senat
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedInheritanceTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseArticleFormFilter extends ObjectCommentableFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['titre'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['titre'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['corps'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['corps'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['user_corps'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['user_corps'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['categorie'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['categorie'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['citoyen_id'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Citoyen'), 'add_empty' => true));
    $this->validatorSchema['citoyen_id'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Citoyen'), 'column' => 'id'));

    $this->widgetSchema   ['article_id'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Article'), 'add_empty' => true));
    $this->validatorSchema['article_id'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Article'), 'column' => 'id'));

    $this->widgetSchema   ['link'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['link'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['status'] = new sfWidgetFormChoice(array('choices' => array('' => '', 'public' => 'public', 'brouillon' => 'brouillon', 'offline' => 'offline')));
    $this->validatorSchema['status'] = new sfValidatorChoice(array('required' => false, 'choices' => array('public' => 'public', 'brouillon' => 'brouillon', 'offline' => 'offline')));

    $this->widgetSchema   ['object_id'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['object_id'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['created_at'] = new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false));
    $this->validatorSchema['created_at'] = new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59'))));

    $this->widgetSchema   ['updated_at'] = new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false));
    $this->validatorSchema['updated_at'] = new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59'))));

    $this->widgetSchema   ['version'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['version'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['slug'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['slug'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema->setNameFormat('article_filters[%s]');
  }

  public function getModelName()
  {
    return 'Article';
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
      'titre' => 'Text',
      'corps' => 'Text',
      'user_corps' => 'Text',
      'categorie' => 'Text',
      'citoyen_id' => 'ForeignKey',
      'article_id' => 'ForeignKey',
      'link' => 'Text',
      'status' => 'Enum',
      'object_id' => 'Number',
      'created_at' => 'Date',
      'updated_at' => 'Date',
      'version' => 'Number',
      'slug' => 'Text',
    ));
  }
}
