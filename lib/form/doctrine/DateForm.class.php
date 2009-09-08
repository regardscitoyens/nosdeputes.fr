<?php

class DateForm extends sfForm
{
  public function configure()
  {
    $this->setWidgets(array('date' => new sfWidgetFormDate()));
    $this->widgetSchema->setNameFormat('date[%s]');
    $this->setValidators(array('date' => new sfValidatorDate()));
  }
}
