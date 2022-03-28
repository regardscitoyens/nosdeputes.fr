<?php

/**
 * Amendement filter form base class.
 *
 * @package    senat
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedInheritanceTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseAmendementFormFilter extends ObjectCommentableFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['source'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['source'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['texteloi_id'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Texteloi'), 'add_empty' => true));
    $this->validatorSchema['texteloi_id'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Texteloi'), 'column' => 'updated_at'));

    $this->widgetSchema   ['numero'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['numero'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['rectif'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['rectif'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['sujet'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['sujet'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['sort'] = new sfWidgetFormChoice(array('choices' => array('' => '', 'Indéfini' => 'Indéfini', 'Adopté' => 'Adopté', 'Irrecevable' => 'Irrecevable', 'Rejeté' => 'Rejeté', 'Retiré' => 'Retiré', 'Tombe' => 'Tombe', 'Non soutenu' => 'Non soutenu', 'Retiré avant séance' => 'Retiré avant séance', 'Rectifié' => 'Rectifié', 'Satisfait' => 'Satisfait')));
    $this->validatorSchema['sort'] = new sfValidatorChoice(array('required' => false, 'choices' => array('Indéfini' => 'Indéfini', 'Adopté' => 'Adopté', 'Irrecevable' => 'Irrecevable', 'Rejeté' => 'Rejeté', 'Retiré' => 'Retiré', 'Tombe' => 'Tombe', 'Non soutenu' => 'Non soutenu', 'Retiré avant séance' => 'Retiré avant séance', 'Rectifié' => 'Rectifié', 'Satisfait' => 'Satisfait')));

    $this->widgetSchema   ['avis_comm'] = new sfWidgetFormChoice(array('choices' => array('' => '', 'Indéfini' => 'Indéfini', 'Favorable' => 'Favorable', 'Défavorable' => 'Défavorable', 'Demande de retrait' => 'Demande de retrait', 'Sagesse' => 'Sagesse')));
    $this->validatorSchema['avis_comm'] = new sfValidatorChoice(array('required' => false, 'choices' => array('Indéfini' => 'Indéfini', 'Favorable' => 'Favorable', 'Défavorable' => 'Défavorable', 'Demande de retrait' => 'Demande de retrait', 'Sagesse' => 'Sagesse')));

    $this->widgetSchema   ['avis_gouv'] = new sfWidgetFormChoice(array('choices' => array('' => '', 'Indéfini' => 'Indéfini', 'Favorable' => 'Favorable', 'Défavorable' => 'Défavorable', 'Demande de retrait' => 'Demande de retrait', 'Sagesse' => 'Sagesse')));
    $this->validatorSchema['avis_gouv'] = new sfValidatorChoice(array('required' => false, 'choices' => array('Indéfini' => 'Indéfini', 'Favorable' => 'Favorable', 'Défavorable' => 'Défavorable', 'Demande de retrait' => 'Demande de retrait', 'Sagesse' => 'Sagesse')));

    $this->widgetSchema   ['date'] = new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate()));
    $this->validatorSchema['date'] = new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDateTime(array('required' => false))));

    $this->widgetSchema   ['signataires'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['signataires'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['texte'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['texte'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['expose'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['expose'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['ref_loi'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['ref_loi'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['organisme_id'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Commission'), 'add_empty' => true));
    $this->validatorSchema['organisme_id'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Commission'), 'column' => 'id'));

    $this->widgetSchema   ['numero_pere'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['numero_pere'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['content_md5'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['content_md5'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['created_at'] = new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false));
    $this->validatorSchema['created_at'] = new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59'))));

    $this->widgetSchema   ['updated_at'] = new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false));
    $this->validatorSchema['updated_at'] = new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59'))));

    $this->widgetSchema   ['parlementaires_list'] = new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'Parlementaire'));
    $this->validatorSchema['parlementaires_list'] = new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'Parlementaire', 'required' => false));

    $this->widgetSchema->setNameFormat('amendement_filters[%s]');
  }

  public function addParlementairesListColumnQuery(Doctrine_Query $query, $field, $values)
  {
    if (!is_array($values))
    {
      $values = array($values);
    }

    if (!count($values))
    {
      return;
    }

    $query
      ->leftJoin($query->getRootAlias().'.ParlementaireAmendement ParlementaireAmendement')
      ->andWhereIn('ParlementaireAmendement.parlementaire_id', $values)
    ;
  }

  public function getModelName()
  {
    return 'Amendement';
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
      'source' => 'Text',
      'texteloi_id' => 'ForeignKey',
      'numero' => 'Text',
      'rectif' => 'Number',
      'sujet' => 'Text',
      'sort' => 'Enum',
      'avis_comm' => 'Enum',
      'avis_gouv' => 'Enum',
      'date' => 'Date',
      'signataires' => 'Text',
      'texte' => 'Text',
      'expose' => 'Text',
      'ref_loi' => 'Text',
      'organisme_id' => 'ForeignKey',
      'numero_pere' => 'Number',
      'content_md5' => 'Text',
      'created_at' => 'Date',
      'updated_at' => 'Date',
      'parlementaires_list' => 'ManyKey',
    ));
  }
}
