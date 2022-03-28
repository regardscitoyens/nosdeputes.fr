<?php

/**
 * Citoyen filter form base class.
 *
 * @package    senat
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseCitoyenFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'login'               => new sfWidgetFormFilterInput(),
      'password'            => new sfWidgetFormFilterInput(),
      'email'               => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'activite'            => new sfWidgetFormFilterInput(),
      'url_site'            => new sfWidgetFormFilterInput(),
      'employe_institution' => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'travail_pour'        => new sfWidgetFormFilterInput(),
      'naissance'           => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'sexe'                => new sfWidgetFormChoice(array('choices' => array('' => NULL, 'H' => 'H', 'F' => 'F'))),
      'nom_circo'           => new sfWidgetFormFilterInput(),
      'num_circo'           => new sfWidgetFormFilterInput(),
      'photo'               => new sfWidgetFormFilterInput(),
      'activation_id'       => new sfWidgetFormFilterInput(),
      'is_active'           => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'role'                => new sfWidgetFormChoice(array('choices' => array('' => '', 'membre' => 'membre', 'moderateur' => 'moderateur', 'admin' => 'admin'))),
      'last_login'          => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'parametres'          => new sfWidgetFormFilterInput(),
      'created_at'          => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at'          => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'slug'                => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'login'               => new sfValidatorPass(array('required' => false)),
      'password'            => new sfValidatorPass(array('required' => false)),
      'email'               => new sfValidatorPass(array('required' => false)),
      'activite'            => new sfValidatorPass(array('required' => false)),
      'url_site'            => new sfValidatorPass(array('required' => false)),
      'employe_institution' => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'travail_pour'        => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'naissance'           => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDateTime(array('required' => false)))),
      'sexe'                => new sfValidatorChoice(array('required' => false, 'choices' => array('' => NULL, 'H' => 'H', 'F' => 'F'))),
      'nom_circo'           => new sfValidatorPass(array('required' => false)),
      'num_circo'           => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'photo'               => new sfValidatorPass(array('required' => false)),
      'activation_id'       => new sfValidatorPass(array('required' => false)),
      'is_active'           => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'role'                => new sfValidatorChoice(array('required' => false, 'choices' => array('membre' => 'membre', 'moderateur' => 'moderateur', 'admin' => 'admin'))),
      'last_login'          => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'parametres'          => new sfValidatorPass(array('required' => false)),
      'created_at'          => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at'          => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'slug'                => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('citoyen_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Citoyen';
  }

  public function getFields()
  {
    return array(
      'id'                  => 'Number',
      'login'               => 'Text',
      'password'            => 'Text',
      'email'               => 'Text',
      'activite'            => 'Text',
      'url_site'            => 'Text',
      'employe_institution' => 'Boolean',
      'travail_pour'        => 'Number',
      'naissance'           => 'Date',
      'sexe'                => 'Enum',
      'nom_circo'           => 'Text',
      'num_circo'           => 'Number',
      'photo'               => 'Text',
      'activation_id'       => 'Text',
      'is_active'           => 'Boolean',
      'role'                => 'Enum',
      'last_login'          => 'Date',
      'parametres'          => 'Text',
      'created_at'          => 'Date',
      'updated_at'          => 'Date',
      'slug'                => 'Text',
    );
  }
}
