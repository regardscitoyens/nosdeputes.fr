<?php

/**
 * Parlementaire filter form base class.
 *
 * @package    senat
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedInheritanceTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseParlementaireFormFilter extends PersonnaliteFormFilter
{
  protected function setupInheritance()
  {
    parent::setupInheritance();

    $this->widgetSchema   ['nom_circo'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['nom_circo'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['num_circo'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['num_circo'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['sites_web'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['sites_web'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['debut_mandat'] = new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate()));
    $this->validatorSchema['debut_mandat'] = new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDateTime(array('required' => false))));

    $this->widgetSchema   ['fin_mandat'] = new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate()));
    $this->validatorSchema['fin_mandat'] = new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDateTime(array('required' => false))));

    $this->widgetSchema   ['place_hemicycle'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['place_hemicycle'] = new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false)));

    $this->widgetSchema   ['url_institution'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['url_institution'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['profession'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['profession'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['autoflip'] = new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no')));
    $this->validatorSchema['autoflip'] = new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0)));

    $this->widgetSchema   ['id_institution'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['id_institution'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['type'] = new sfWidgetFormChoice(array('choices' => array('' => '', 'depute' => 'depute', 'senateur' => 'senateur')));
    $this->validatorSchema['type'] = new sfValidatorChoice(array('required' => false, 'choices' => array('depute' => 'depute', 'senateur' => 'senateur')));

    $this->widgetSchema   ['groupe_acronyme'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['groupe_acronyme'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['parti'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['parti'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['adresses'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['adresses'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['suppleant_de_id'] = new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('SuppleantDe'), 'add_empty' => true));
    $this->validatorSchema['suppleant_de_id'] = new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('SuppleantDe'), 'column' => 'id'));

    $this->widgetSchema   ['anciens_mandats'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['anciens_mandats'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['autres_mandats'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['autres_mandats'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['mails'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['mails'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['top'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['top'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['villes'] = new sfWidgetFormFilterInput();
    $this->validatorSchema['villes'] = new sfValidatorPass(array('required' => false));

    $this->widgetSchema   ['organismes_list'] = new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'Organisme'));
    $this->validatorSchema['organismes_list'] = new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'Organisme', 'required' => false));

    $this->widgetSchema   ['amendements_list'] = new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'Amendement'));
    $this->validatorSchema['amendements_list'] = new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'Amendement', 'required' => false));

    $this->widgetSchema   ['textelois_list'] = new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'Texteloi'));
    $this->validatorSchema['textelois_list'] = new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'Texteloi', 'required' => false));

    $this->widgetSchema->setNameFormat('parlementaire_filters[%s]');
  }

  public function addOrganismesListColumnQuery(Doctrine_Query $query, $field, $values)
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
      ->leftJoin($query->getRootAlias().'.ParlementaireOrganisme ParlementaireOrganisme')
      ->andWhereIn('ParlementaireOrganisme.organisme_id', $values)
    ;
  }

  public function addAmendementsListColumnQuery(Doctrine_Query $query, $field, $values)
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
      ->andWhereIn('ParlementaireAmendement.amendement_id', $values)
    ;
  }

  public function addTexteloisListColumnQuery(Doctrine_Query $query, $field, $values)
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
      ->andWhereIn('ParlementaireTexteloi.texteloi_id', $values)
    ;
  }

  public function getModelName()
  {
    return 'Parlementaire';
  }

  public function getFields()
  {
    return array_merge(parent::getFields(), array(
      'nom_circo' => 'Text',
      'num_circo' => 'Number',
      'sites_web' => 'Text',
      'debut_mandat' => 'Date',
      'fin_mandat' => 'Date',
      'place_hemicycle' => 'Number',
      'url_institution' => 'Text',
      'profession' => 'Text',
      'autoflip' => 'Boolean',
      'id_institution' => 'Text',
      'type' => 'Enum',
      'groupe_acronyme' => 'Text',
      'parti' => 'Text',
      'adresses' => 'Text',
      'suppleant_de_id' => 'ForeignKey',
      'anciens_mandats' => 'Text',
      'autres_mandats' => 'Text',
      'mails' => 'Text',
      'top' => 'Text',
      'villes' => 'Text',
      'organismes_list' => 'ManyKey',
      'amendements_list' => 'ManyKey',
      'textelois_list' => 'ManyKey',
    ));
  }
}
