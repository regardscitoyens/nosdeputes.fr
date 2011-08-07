<?php
class UploadAvatarForm extends BaseForm
{
  public function configure()
  {
    $this->widgetSchema->setNameFormat('upload[%s]');
		
		$this->widgetSchema['photo'] = new sfWidgetFormInputFile();
    $this->validatorSchema['photo'] = new sfValidatorFile(array(
		'required' => false, 
		'mime_types' => array('image/jpeg', 'image/pjpeg', 'image/png', 'image/bmp')
		), array(
		'invalid' => 'Fichier invalide', 
    'mime_types' => 'L\'image doit &ecirc;tre de type JPG/PNG/BMP.', 
		'max_size' => '2Mo maxi'
		));
		
    // label
    $this->widgetSchema->setLabels(array(
      'photo' => 'Photo/Avatar : '
    ));
  }
  
}
?>