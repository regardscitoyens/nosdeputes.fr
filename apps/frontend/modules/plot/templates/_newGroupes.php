<div id="overDiv"></div>
<?php $PictureID = "Map_".rand(1,10000).".map"; ?>
<?php if ($type === "home") echo '<a href="'.url_for('@top_global').'">'; ?>
<img id="graph_groupes" alt="Répartition de l'activité parlementaire sur l<?php echo myTools::getTextEndDataWithShortContext(); ?>" src="<?php echo url_for('@groupes_plot_graph?type='.$type).'?mapId='.$PictureID; ?>" onmousemove="getMousePosition(event);" onmouseout="nd();"/>
<?php if ($type === "home") echo '</a>'; ?>
<script type="text/javascript">
<!--
LoadImageMap("graph_groupes", "<?php echo url_for('@groupes_plot_graph?type='.$type).'?drawAction=map&mapId='.$PictureID; ?>");
//-->
</script>
<?php echo include_partial('plot/groupesLegende', array()); ?>
