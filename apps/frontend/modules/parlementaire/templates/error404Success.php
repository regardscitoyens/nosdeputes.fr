<?php
if (sfConfig::get('app_host_previous_legislature')) {
header("Location: http://".sfConfig::get('app_host_previous_legislature').$_SERVER['REQUEST_URI']."\n");
exit;
}
?><h1>Nous n'avons pas pu trouver la page demandée</h1>
