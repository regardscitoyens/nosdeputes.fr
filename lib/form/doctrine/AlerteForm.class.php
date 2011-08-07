<?php

/**
 * Alerte form.
 *
 * @package    cpc
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class AlerteForm extends BaseAlerteForm
{
  public function configure()
  {
    unset($this->widgetSchema['confirmed']);
    unset($this->widgetSchema['next_mail']);
    unset($this->widgetSchema['last_mail']);
    unset($this->widgetSchema['verif']);
    unset($this->widgetSchema['created_at']);
    unset($this->widgetSchema['updated_at']);
    unset($this->widgetSchema['titre']);
    unset($this->widgetSchema['citoyen_id']);
    unset($this->widgetSchema['no_human_query']);
    unset($this->widgetSchema['query_md5']);

    unset($this->validatorSchema['confirmed']);
    unset($this->validatorSchema['next_mail']);
    unset($this->validatorSchema['last_mail']);
    unset($this->validatorSchema['verif']);
    unset($this->validatorSchema['created_at']);
    unset($this->validatorSchema['updated_at']);
    unset($this->validatorSchema['titre']);
    unset($this->validatorSchema['citoyen_id']);
    unset($this->validatorSchema['no_human_query']);
    unset($this->validatorSchema['query_md5']);

    $this->widgetSchema['query'] = new sfWidgetFormInput();
    $this->widgetSchema['filter'] = new sfWidgetFormInputHidden();

    $this->widgetSchema['period'] = new sfWidgetFormChoice(array('choices' => array('HOUR' => 'Une fois par heure', 'DAY' => 'Une fois par jour', 'WEEK' => 'Une fois par semaine', 'MONTH' => 'Une fois par mois')));
    $this->setDefault('period', 'WEEK');

    $this->widgetSchema->setLabels(array(
					 'email'    => 'Votre email',
					 'query'   => 'Mots clés recherchés',
					 'period' => 'Période max. de réception',
					 )
				   );

    $this->validatorSchema['query'] = new sfValidatorString(array('required' => true), array('required' => 'Merci d\'indiquer vos mots clés'));
    $this->validatorSchema['id'] = new sfValidatorDoctrineChoice(array('model' => 'Alerte', 'column' => 'id', 'required' => false));

    if ($this->getObject()->no_human_query) {
      $this->widgetSchema['query'] = new sfWidgetFormInputHidden();
    }

    if ($this->getObject()->citoyen_id) {
      unset($this->widgetSchema['email']);
      unset($this->validatorSchema['email']);
    }else {
      $this->validatorSchema['email'] = new sfValidatorEmail(array('required' => true), array('required' => 'Email obligatoire'));
    }
  }
}
