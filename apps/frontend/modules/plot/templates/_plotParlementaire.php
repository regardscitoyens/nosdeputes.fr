<?php
$size='';
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
$PictureID = "Map_".$parlementaire->slug.'_'.rand(1,10000).".map"; 
if ($link === 'true') {
  $time = 'lastyear';
  if (myTools::isFinLegislature()) $time = 'legislature';
  echo '<a href="'.url_for('@parlementaire_plot?slug='.$parlementaire->slug.'&time=legislature').'">';
  $size = 'height:150px; width:800px';
 } else echo '<div class="par_session">'; ?>
 <img style="<?php echo $size; ?>" id="graph<?php echo $PictureID; ?>" alt="Participation <?php echo $titre; ?> de <?php echo $parlementaire->nom; ?>" src="<?php echo url_for('@parlementaire_plot_graph?slug='.$parlementaire->slug.'&time='.$time.'&type='.$type.'&questions='.$questions.'&link='.$link.'&mapId='.$PictureID); ?>" onmousemove="getMousePosition(event);" onmouseout="nd();"/>
<?php if ($link === 'true') { ?>
<script type="text/javascript">
<!--
LoadImageMap("graph<?php echo $PictureID; ?>", "<?php echo url_for('@parlementaire_plot_graph?slug='.$parlementaire->slug.'&time='.$time.'&type='.$type.'&questions='.$questions.'&link='.$link.'&drawAction=map&mapId='.$PictureID); ?>");
//-->
</script>
<?php } ?>

<?php if ($link === 'true') echo '</a>';
echo "<p><span style='background-color: rgb(255,0,0);'>&nbsp;</span> ";
if ($type === 'commission') echo '&nbsp;Présences enregistrées&nbsp;&nbsp;&nbsp;';
else echo '&nbsp;Présences relevées&nbsp;&nbsp;&nbsp;';
echo "<span style='background-color: rgb(255,200,0);'>&nbsp;</span>&nbsp;Participations&nbsp;&nbsp;&nbsp;";
echo "<span style='background-color: rgb(0,255,0);'>&nbsp;</span>&nbsp;Mots prononcés (x&nbsp;10&nbsp;000)&nbsp;&nbsp;&nbsp;";
if (!(myTools::isFinLegislature() && preg_match('/^l/', $time)) && $questions === 'true' && $type != 'commission')
    echo "<span style='background-color: rgb(0,0,255);'>&nbsp;</span>&nbsp;Questions orales<br/>";
echo "<span style='background-color: rgb(150,150,150);'>&nbsp;</span>&nbsp;Vacances parlementaires&nbsp;&nbsp;";
echo "<span style='font-weight: bolder; color: rgb(200,200,200);'>&mdash;</span>&nbsp;Présence médiane";
if ($link === 'true')
  echo '&nbsp;&nbsp;&nbsp;&nbsp;<a href="'.url_for('@faq').'#post_4">Explications</a></p>';
else echo '</p>';
if ($link != 'true')
  echo '</div>';
?>
