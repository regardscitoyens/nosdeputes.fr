<?php
if (!$width) $width = 384.98181 * $height/447.31604;
if (!$height) $height = 447.31604 * $width/384.98181;
if (isset($link)) echo '<a href="'.url_for('@list_parlementaires_circo').'">';
$svg = file_get_contents('departements.svg');
$svg = str_replace("\n", "", $svg);
$svg = preg_replace('/width="[^"]*"/', 'width="'.$width.'"', $svg);
$svg = preg_replace('/height="[^"]*"/', 'height="'.$height.'"', $svg);
echo preg_replace_callback('/<(g|path)[^>]*id="d(\d+)".*?<\/\\1>/', function($m) { return '<a xlink:href="'.url_for('@list_parlementaires_circo_search?search='.$m[2]).'">'.$m[0].'</a>'; }, $svg);
if (isset($link)) echo '</a>'; ?>
