<?php

// this check prevents access to debug front controllers that are deployed by accident to production servers.
// feel free to remove this, extend it or make something more sophisticated.
if (!in_array(@$_SERVER['HTTP_CF_CONNECTING_IP'], array('127.0.0.1', 
                '109.190.91.186', //Tangui (Paris) 
                '88.168.238.200', //Benjamin (Paris)
                '93.7.233.149')) && !in_array(@$_SERVER['REMOTE_ADDR'], array('127.0.0.1',
                '109.190.91.186', //Tangui (Paris) 
                '88.168.238.200', //Benjamin (Paris) 
                '88.181.216.59', //Tangui (Nantes) 
                '80.13.217.126', //Tangui (April) 
                '88.191.92.80', //Serveur dÃ©diÃ© 
                '78.232.200.105', //Tangui Rennes 
                '78.29.235.101', //Tangui Bx 
                '::1')))
{
  die('You are not allowed to access this file. Please remove "/frontend_dev.php/" from the url you are trying to access.');
}

require_once(dirname(__FILE__).'/../config/ProjectConfiguration.class.php');

$configuration = ProjectConfiguration::getApplicationConfiguration('frontend', 'dev', true);
sfContext::createInstance($configuration)->dispatch();
