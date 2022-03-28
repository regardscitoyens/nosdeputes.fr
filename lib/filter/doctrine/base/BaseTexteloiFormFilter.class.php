<?php

/**
 * Texteloi filter form base class.
 *
 * @package    senat
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedInheritanceTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseTexteloiFormFilter extends ObjectCommentableFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['numero'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['numero'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['annexe'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['annexe'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['type'] = new sfWidgetFormChoice(array('choices' => array('' => '', 'Motion' => 'Motion', 'Proposition de loi' => 'Proposition de loi', 'Proposition de résolution' => 'Proposition de résolution', 'Projet de loi' => 'Projet de loi', 'Texte adopté' => 'Texte adopté', 'Texte de la commission' => 'Texte de la commission', 'Texte de la commission mixte paritaire' => 'Texte de la commission mixte paritaire', 'Lettre' => 'Lettre', 'Rapport' => 'Rapport', 'Rapport d\'information' => 'Rapport d\'information', 'Rapport d\'office parlementaire' => 'Rapport d\'office parlementaire', 'Avis' => 'Avis', 'Rapport de groupe d\'amitié' => 'Rapport de groupe d\'amitié', 'Rapport de commission d\'enquête' => 'Rapport de commission d\'enquête')));
    $this->validatorSchema['type'] = new sfValidatorChoice(array('required' => false, 'choices' => array('Motion' => 'Motion', 'Proposition de loi' => 'Proposition de loi', 'Proposition de résolution' => 'Proposition de résolution', 'Projet de loi' => 'Projet de loi', 'Texte adopté' => 'Texte adopté', 'Texte de la commission' => 'Texte de la commission', 'Texte de la commission mixte paritaire' => 'Texte de la commission mixte paritaire', 'Lettre' => 'Lettre', 'Rapport' => 'Rapport', 'Rapport d\'information' => 'Rapport d\'information', 'Rapport d\'office parlementaire' => 'Rapport d\'office parlementaire', 'Avis' => 'Avis', 'Rapport de groupe d\'amitié' => 'Rapport de groupe d\'amitié', 'Rapport de commission d\'enquête' => 'Rapport de commission d\'enquête')));

    $this->widgetSchema   ['type_details'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['type_details'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['categorie'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['categorie'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['id_dossier_institution'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['id_dossier_institution'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['titre'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['titre'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['date'] = new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate()));
    $this->validatorSchema['date'] = new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDateTime(array('required' => false))));

    $this->widgetSchema   ['source'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['source'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['organisme_id'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Organisme'), 'add_empty' => true));
    $this->validatorSchema['organisme_id'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Organisme'), 'column' => 'id'));

    $this->widgetSchema   ['signataires'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['signataires'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['contenu'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['contenu'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['created_at'] = new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false));
    $this->validatorSchema['created_at'] = new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59'))));

    $this->widgetSchema   ['updated_at'] = new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false));
    $this->validatorSchema['updated_at'] = new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59'))));

    $this->widgetSchema   ['parlementaires_list'] = new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'Parlementaire'));
    $this->validatorSchema['parlementaires_list'] = new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'Parlementaire', 'required' => false));

    $this->widgetSchema->setNameFormat('texteloi_filters[%s]');
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
      ->leftJoin($query->getRootAlias().'.ParlementaireTexteloi ParlementaireTexteloi')
      ->andWhereIn('ParlementaireTexteloi.parlementaire_id', $values)
    ;
  }

  public function getModelName()
  {
    return 'Texteloi';
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
      'numero' => 'Number',
      'annexe' => 'Text',
      'type' => 'Enum',
      'type_details' => 'Text',
      'categorie' => 'Text',
      'id_dossier_institution' => 'Text',
      'titre' => 'Text',
      'date' => 'Date',
      'source' => 'Text',
      'organisme_id' => 'ForeignKey',
      'signataires' => 'Text',
      'contenu' => 'Text',
      'created_at' => 'Date',
      'updated_at' => 'Date',
      'parlementaires_list' => 'ManyKey',
    ));
  }
}
