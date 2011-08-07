<?php 
if (!class_exists('CirconscriptionActions'))
  include(sfConfig::get('sf_app_dir').'/modules/circonscription/actions/actions.class.php');
if (!isset($link)) $link = false;
CirconscriptionActions::echoDeptmtsMap($width, $height, $link);
