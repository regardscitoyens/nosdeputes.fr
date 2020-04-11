<?php

$link = mysql_connect('localhost', 'sanctions', 'sanctions');
if (!$link) {
    die('Could not connect: ' . mysql_error());
}

mysql_select_db('sanctions');

$montant = 353.2425;
