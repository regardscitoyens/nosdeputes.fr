<?php

/**
 * ArticleVersion filter form base class.
 *
 * @package    senat
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseArticleVersionFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'nb_commentaires' => new sfWidgetFormFilterInput(),
      'titre'           => new sfWidgetFormFilterInput(),
      'corps'           => new sfWidgetFormFilterInput(),
      'user_corps'      => new sfWidgetFormFilterInput(),
      'categorie'       => new sfWidgetFormFilterInput(),
      'citoyen_id'      => new sfWidgetFormFilterInput(),
      'article_id'      => new sfWidgetFormFilterInput(),
      'link'            => new sfWidgetFormFilterInput(),
      'status'          => new sfWidgetFormChoice(array('choices' => array('' => '', 'public' => 'public', 'brouillon' => 'brouillon', 'offline' => 'offline'))),
      'object_id'       => new sfWidgetFormFilterInput(),
      'created_at'      => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at'      => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
    ));

    $this->setValidators(array(
      'nb_commentaires' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'titre'           => new sfValidatorPass(array('required' => false)),
      'corps'           => new sfValidatorPass(array('required' => false)),
      'user_corps'      => new sfValidatorPass(array('required' => false)),
      'categorie'       => new sfValidatorPass(array('required' => false)),
      'citoyen_id'      => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'article_id'      => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'link'            => new sfValidatorPass(array('required' => false)),
      'status'          => new sfValidatorChoice(array('required' => false, 'choices' => array('public' => 'public', 'brouillon' => 'brouillon', 'offline' => 'offline'))),
      'object_id'       => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'created_at'      => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at'      => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
    ));

    $this->widgetSchema->setNameFormat('article_version_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'ArticleVersion';
  }

  public function getFields()
  {
    return array(
      'id'              => 'Number',
      'nb_commentaires' => 'Number',
      'titre'           => 'Text',
      'corps'           => 'Text',
      'user_corps'      => 'Text',
      'categorie'       => 'Text',
      'citoyen_id'      => 'Number',
      'article_id'      => 'Number',
      'link'            => 'Text',
      'status'          => 'Enum',
      'object_id'       => 'Number',
      'created_at'      => 'Date',
      'updated_at'      => 'Date',
      'version'         => 'Number',
    );
  }
}
