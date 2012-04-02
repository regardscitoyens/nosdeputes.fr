<?php

// this check prevents access to debug front controllers that are deployed by accident to production servers.
// feel free to remove this, extend it or make something more sophisticated.
if (!in_array(@$_SERVER['HTTP_CF_CONNECTING_IP'], array('127.0.0.1',
                '82.225.240.116', //Tangui (Paris)
                '88.168.238.200', //Benjamin (Paris))) || !in_array(@$_SERVER['REMOTE_ADDR'], array('127.0.0.1','173.245.49.172', 
		'82.225.240.116', //Tangui (Paris)
                '88.168.238.200', //Benjamin (Paris)
                '129.199.114.20', //Benjamin (Paris, ENS)
                '77.205.50.174', //Brice 
					      '::1', )))
{
  die('You ('.@$_SERVER['REMOTE_ADDR'].')are not allowed to access this file. Check '.basename(__FILE__).' for more information.');
}

require_once(dirname(__FILE__).'/../config/ProjectConfiguration.class.php');

$configuration = ProjectConfiguration::getApplicationConfiguration('backend', 'dev', true);
sfContext::createInstance($configuration)->dispatch();
