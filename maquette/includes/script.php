<?php
// Détection style
$style_defaut = 'fixe'; // sera obtenu via variable objet
$styles = array('defaut', 'fixe'); // sera obtenu en scannant le dossier css (nom de dossier = nom de style)

if (isset($_COOKIE["style"])) {
	$style = htmlentities($_COOKIE["style"], ENT_QUOTES);
	if (!in_array($style, $styles)) { $style = $style_defaut; }
}
if (isset($_POST["style"])) {
	$style = htmlentities($_POST["style"], ENT_QUOTES);
	if (in_array($style, $styles)) { setcookie("style", $style, false, "/", false); }
	else { $style = $style_defaut; }
}
else { $style = $style_defaut; }
// Fin détection style
?>