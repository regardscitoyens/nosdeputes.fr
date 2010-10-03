<?php 
if (!class_exists('CirconscriptionActions'))
  include(sfConfig::get('sf_app_dir').'/modules/circonscription/actions/actions.class.php');
CirconscriptionActions::echoDeptmtsMap($circo, $height, $width);