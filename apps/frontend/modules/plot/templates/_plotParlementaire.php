<?php
if ($time === 'lastyear')
  $shortduree = 'annee';
else $shortduree = $time;
if ($type === 'total')
  $titre = 'globale-'.$shortduree;
else {
  $titre = $type;
  if ($type === 'commission') $titre .= 's';
  $titre .= '-'.$shortduree;
}
$PictureID = "Map_".rand(1,10000).".map"; ?>

<?php if ($link === 'true') echo '<a href="'.url_for('@parlementaire_plot?slug='.$parlementaire->slug.'&time=lastyear').'">';
else echo '<div class="par_session">'; ?>
<img id="graph<?php echo $PictureID; ?>" alt="Participation <?php echo $titre; ?> de <?php echo $parlementaire->nom; ?>" src="<?php echo url_for('@parlementaire_plot_graph?slug='.$parlementaire->slug.'&time='.$time.'&type='.$type.'&questions='.$questions.'&link='.$link.'&mapId='.$PictureID); ?>" OnMouseMove="getMousePosition(event);" OnMouseOut="nd();"/>
<?php if ($link === 'true') { ?>
<script> LoadImageMap("graph<?php echo $PictureID; ?>", "<?php echo url_for('@parlementaire_plot_graph?slug='.$parlementaire->slug.'&time='.$time.'&type='.$type.'&questions='.$questions.'&link='.$link.'&drawAction=map&mapId='.$PictureID); ?>"); </script>
<?php } ?>

<?php if ($link === 'true') echo '</a>';
echo "<p><span style='background-color: rgb(255,0,0);'>&nbsp;</span> ";
if ($type === 'commission') echo '&nbsp;Présences enregistrées&nbsp;&nbsp;&nbsp;';
else echo '&nbsp;Présences relevées&nbsp;&nbsp;&nbsp;';
echo "<span style='background-color: rgb(255,200,0);'>&nbsp;</span>&nbsp;Participations&nbsp;&nbsp;&nbsp;";
echo "<span style='background-color: rgb(0,255,0);'>&nbsp;</span>&nbsp;Mots prononcés (x&nbsp;10&nbsp;000)&nbsp;&nbsp;&nbsp;";
if ($questions === 'true' && $type != 'commission')
    echo "<span style='background-color: rgb(0,0,255);'>&nbsp;</span>&nbsp;Questions orales&nbsp;&nbsp;&nbsp;";
echo "<span style='background-color: rgb(150,150,150);'>&nbsp;</span>&nbsp;Vacances parlementaires";
if ($link === 'true')
  echo "&nbsp;&nbsp;&nbsp;&nbsp;".link_to('Explications', '@parlementaire_plot?slug='.$parlementaire->slug.'&time=lastyear#explications').'</p>';
else echo '</p>';
if ($link != 'true')
  echo '</div>';
?>
