<?php
if (sfConfig::get('app_redirect404tohost')) {
header("Location: http://".sfConfig::get('app_redirect404tohost').$_SERVER['REQUEST_URI']."\n");
exit;
}
?><h1>Nous n'avons pas pu trouver la page demandée</h1>
