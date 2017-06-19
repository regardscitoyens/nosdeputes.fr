<div id="overDiv"></div>
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
