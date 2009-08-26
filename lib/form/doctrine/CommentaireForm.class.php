<?php

/**
 * Commentaire form.
 *
 * @package    form
 * @subpackage Commentaire
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 6174 2007-11-27 06:22:40Z fabien $
 */
class CommentaireForm extends BaseCommentaireForm
{
  public function configure()
  {
    unset(
	  $this['citoyen_id'],
	  $this['updated_at'],
	  $this['created_at'],
	  $this['is_public'],
	  $this['rate'],
	  $this['object_type'],
	  $this['object_id']
	  );
    $this->validatorSchema['commentaire']->setOption('required', true);
    $this->widgetSchema->setFormFormatterName('table');

  }
}