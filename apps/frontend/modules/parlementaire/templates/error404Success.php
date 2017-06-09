<?php
$prevHost = myTools::getPreviousHost();
$uri = $_SERVER['REQUEST_URI'];
if ($prevHost &&
  (preg_match('/^\/1\d\//', $uri) || preg_match('/^\/[^\/]+$/', $uri)) &&
  !preg_match('/^\/'.myTools::getLegislature().'\//', $uri)
) {
  header("Location: ".myTools::getProtocol()."://".$prevHost.$uri."\n");
  exit;
}
header("Status: 404 Not found");
?><h1>Nous n'avons pas pu trouver la page demandée</h1>
