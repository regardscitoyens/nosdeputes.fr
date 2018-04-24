<?php

require_once(dirname(__FILE__).'/../config/ProjectConfiguration.class.php');
$configuration = ProjectConfiguration::getApplicationConfiguration('frontend', 'dev', true);

// this check prevents access to debug front controllers that are deployed by accident to production servers.
if (!myTools::isAdminIP(@$_SERVER)) {
  die('You ('.$_SERVER['REMOTE_ADDR'].' / '.$_SERVER['HTTP_X_FORWARDED_FOR'].') are not allowed to access this file. Check '.basename(__FILE__).' for more information.');
}

sfContext::createInstance($configuration)->dispatch();
