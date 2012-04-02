<?php
// this check prevents access to debug front controllers that are deployed by accident to production servers.
// feel free to remove this, extend it or make something more sophisticated.
if ( !in_array(@$_SERVER['HTTP_CF_CONNECTING_IP'], array('127.0.0.1',
                '82.225.240.116', //Tangui (Paris)
                '88.168.238.200', //Benjamin (Paris))) || !in_array(@$_SERVER['REMOTE_ADDR'], array('127.0.0.1',
		'82.225.240.116', //Tangui (Paris)
		'88.168.238.200', //Benjamin (Paris)
                '129.199.114.20', //Benjamin (Paris, ENS)
                '145.238.180.6' , // Benjamin Meudon
		'88.181.216.59', //Tangui (Nantes)
		'80.13.217.126', //Tangui (April)
		'88.191.92.80', //Serveur dédié
		'78.232.200.105', //Tangui Rennes
		'78.29.235.101', //Tangui Bx
		'::1')))
{
//  die('You are not allowed to access this file. Check '.basename(__FILE__).' for more information.');
}

require_once(dirname(__FILE__).'/../config/ProjectConfiguration.class.php');

$configuration = ProjectConfiguration::getApplicationConfiguration('frontend', 'dev', true);
sfContext::createInstance($configuration)->dispatch();
