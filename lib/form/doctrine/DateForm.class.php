<?php

class DateForm extends BaseForm
{
  public function configure()
  {
    $this->setWidgets(array('date' => new sfWidgetFormDate()));
    $this->widgetSchema->setNameFormat('date[%s]');
    $this->setValidators(array('date' => new sfValidatorDate()));
  }
}
