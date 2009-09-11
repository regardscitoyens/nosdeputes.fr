<?php
class UploadAvatarForm extends sfForm
{
  public function configure()
  {
    $this->widgetSchema->setNameFormat('upload[%s]');
		
		$this->widgetSchema['photo'] = new sfWidgetFormInputFile();
    $this->validatorSchema['photo'] = new sfValidatorFile(array(
		'required' => false, 
    'mime_types' => array('image/jpeg', 'image/pjpeg')
		), array(
		'invalid' => 'Fichier invalide', 
    'mime_types' => 'L\'image doit être de type JPG.', 
		'max_size' => '2Mo maxi'
		));
		
    // label
    $this->widgetSchema->setLabels(array(
      'photo' => 'Photo/Avatar : '
    ));
  }
  
}
?>