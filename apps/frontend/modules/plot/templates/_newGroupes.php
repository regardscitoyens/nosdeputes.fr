<?php
  //Si c'est la home, on affiche la div
  if ($type === "home") :

  //Vérifie qu'il y a des données, sinon n'affiche pas le bloc
  $data = unserialize(get_component('plot', 'getGroupesData', array('type' => $type)));
  if (!isset($data['groupes']) || !count($data['groupes'])) return ;
?>
<div class="box_repartition aligncenter"><div style="margin: auto;">
<h2><span style="margin-right: 5px;"><img alt="activite" src="<?php echo $sf_request->getRelativeUrlRoot(); ?>/images/xneth/ico_graph.png" /></span><a href="<?php echo url_for('@top_global'); ?>#groupes">Activité parlementaire <?php
if (myTools::isFinLegislature()) {
echo 'de la législature';
}else{
$mois = min(12, floor((time() - strtotime(myTools::getDebutLegislature())) / (60*60*24*30)));
echo ($mois < 2 ? "du premier" : "des $mois ".($mois < 12 ? "prem" : "dern")."iers")." mois";
}?></a></h2>
<div id="overDiv"></div>
<?php endif; //Fin d'entete div pour la HOME ?>
<?php $PictureID = "Map_".rand(1,10000).".map"; ?>
<?php $style = 'width:720px;height:336px;'; if ($type === "home") {echo '<a href="'.url_for('@top_global').'#groupes">'; $style="width:433px; height:320px;";}?>
<img style="<?php echo $style; ?>" id="graph_groupes" alt="Répartition de l'activité parlementaire <?php
if (myTools::isFinLegislature()) {
  echo 'sur l\'ensemble de la législature';
} else{
  echo 'sur les 12 derniers mois';
}?>" src="<?php echo url_for('@groupes_plot_graph?type='.$type.'&mapId='.$PictureID); ?>" onmousemove="getMousePosition(event);" onmouseout="nd();"/>
<?php if ($type === "home") echo '</a>'; ?>
<script type="text/javascript">
<!--
LoadImageMap("graph_groupes", "<?php echo url_for('@groupes_plot_graph?type='.$type.'&drawAction=map&mapId='.$PictureID); ?>");
//-->
</script>
<?php echo include_partial('plot/groupesLegende', array()); ?>
<?php if ($type === "home") : //Si c'est la home, on ferme les div consacrées ?>
</div></div>
<?php endif; ?>
