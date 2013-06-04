<?php
if (sfConfig::get('app_host_previous_legislature') && !preg_match('/^\/'.sfConfig::get('app_legislature').'\//', $_SERVER['REQUEST_URI'])) {
header("Location: http://".sfConfig::get('app_host_previous_legislature').$_SERVER['REQUEST_URI']."\n");
exit;
}
header("Status: 404 Not found");
?><h1>Nous n'avons pas pu trouver la page demandée</h1>
