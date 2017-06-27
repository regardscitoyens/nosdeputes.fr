<?php
// Précharge les données du graphe qui seront ensuite cachées
$data = unserialize(get_component('plot', 'getGroupesData', array('type' => $type)));

//Si c'est la home, on affiche la div uniquement s'il y des données
if ($type === "home") :
  if (!$data['hasData']) return;
?>
<div class="box_repartition aligncenter"><div style="margin: auto;">
<h2><span style="margin-right: 5px;"><img alt="activite" src="<?php echo $sf_request->getRelativeUrlRoot(); ?>/images/xneth/ico_graph.png" /></span><a href="<?php echo url_for('@top_global'); ?>#groupes">Activité parlementaire <?php
if (myTools::isFinLegislature()) {
  echo 'de la législature';
} else {
  $mois = min(12, floor((time() - strtotime(myTools::getDebutLegislature())) / (60*60*24*30)));
  echo ($mois < 2 ? "du premier" : "des $mois ".($mois < 12 ? "prem" : "dern")."iers")." mois";
} ?></a></h2>
<?php endif; //Fin d'entete div pour la home

$PictureID = "Map_".rand(1,10000).".map";
$w = 720;
$h = 336;
if ($type === "home") {
  echo '<a href="'.url_for('@top_global').'#groupes">';
  $w = 433;
  $h = 320;
}
$style = "width: ".$w."px; height: ".$h."px;";
?>
<img style="<?php echo $style; ?>" id="graph_groupes" alt="Répartition de l'activité parlementaire sur <?php
echo (myTools::isFinLegislature() ? "l'ensemble de la législature" : "les 12 derniers mois");
?>" src="<?php echo url_for('@groupes_plot_graph?type='.$type.'&mapId='.$PictureID); ?>" onmousemove="getMousePosition(event);" onmouseout="nd();"/>
<div id="overDiv"></div>
<?php if ($type === "home") echo '</a>'; ?>
<?php echo include_partial('plot/groupesLegende', array("groupes" => array_keys($data['groupes']), "width" => $w)); ?>
<script type="text/javascript">
LoadImageMap("graph_groupes", "<?php echo url_for('@groupes_plot_graph?type='.$type.'&format=map&mapId='.$PictureID); ?>");
</script>
<?php if ($type === "home") : //Si c'est la home, on ferme les div consacrées ?>
</div></div>
<?php endif; ?>
