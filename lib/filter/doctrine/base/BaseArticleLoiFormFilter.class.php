<?php

/**
 * ArticleLoi filter form base class.
 *
 * @package    senat
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedInheritanceTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseArticleLoiFormFilter extends ObjectCommentableFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['texteloi_id'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Texteloi'), 'add_empty' => true));
    $this->validatorSchema['texteloi_id'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Texteloi'), 'column' => 'updated_at'));

    $this->widgetSchema   ['titre'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['titre'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['ordre'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['ordre'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['precedent'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['precedent'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['suivant'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['suivant'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['expose'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['expose'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['titre_loi_id'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('TitreLoi'), 'add_empty' => true));
    $this->validatorSchema['titre_loi_id'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('TitreLoi'), 'column' => 'id'));

    $this->widgetSchema   ['slug'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['slug'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema->setNameFormat('article_loi_filters[%s]');
  }

  public function getModelName()
  {
    return 'ArticleLoi';
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
      'texteloi_id' => 'ForeignKey',
      'titre' => 'Text',
      'ordre' => 'Number',
      'precedent' => 'Text',
      'suivant' => 'Text',
      'expose' => 'Text',
      'titre_loi_id' => 'ForeignKey',
      'slug' => 'Text',
    ));
  }
}
