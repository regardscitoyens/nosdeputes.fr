<?php
$link = mysql_connect('localhost', 'confiseurs', 'EuroTasse');
if (!$link) {
    die('Could not connect: ' . mysql_error());
}

mysql_select_db('confiseurs');

$montant = 353.2425;
