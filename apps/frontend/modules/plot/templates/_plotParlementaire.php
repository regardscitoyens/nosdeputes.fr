<?php
$abs = '';
if (!isset($target))
  $target = '';
if (isset($absolute) && $absolute)
  $abs = 'absolute=true';
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
if ($link === 'true') {
  $time = 'lastyear';
  if (myTools::isFinLegislature()) $time = 'legislature';
  echo '<a'.$target.' href="'.url_for('@parlementaire'.(isset($absolute) && $absolute ? '' : '_plot').'?slug='.$parlementaire->slug.(isset($absolute) && $absolute ? '' : '&time='.(myTools::isFinLegislature() ? 'legislature' : 'lastyear')), $abs).'">';
  if (!isset($widthrate)) $widthrate = 1;
  $size = 'height: '.floor(150*$widthrate).'px; width: '.floor(795*$widthrate).'px;';
 } else echo '<div class="par_session">'; ?>
 <img style="<?php echo $size; ?>" alt="Participation <?php echo $titre; ?> de <?php echo $parlementaire->nom; ?>" src="<?php echo url_for('@parlementaire_plot_graph?slug='.$parlementaire->slug.'&time='.$time.'&type='.$type.'&questions='.$questions.'&link='.$link, $abs); ?>"<?php if (!(isset($absolute) && $absolute)) echo ' onmousemove="getMousePosition(event);" onmouseout="nd();"'; ?>/>
<?php if ($link === 'true') echo '</a>';
if (!(isset($absolute) && $absolute)) : ?>
<script type="text/javascript">
</script>
<?php endif;

if (!isset($widthrate) || $widthrate > 1/3) {
echo "<p><span style='background-color: rgb(255,0,0);'>&nbsp;</span> ";
if ($type === 'commission') echo '&nbsp;Présences enregistrées&nbsp;&nbsp;&nbsp;';
else echo '&nbsp;Présences relevées&nbsp;&nbsp;&nbsp;';
echo "<span style='background-color: rgb(255,200,0);'>&nbsp;</span>&nbsp;Participations&nbsp;&nbsp;&nbsp;";
if (!(myTools::isFinLegislature() && preg_match('/^l/', $time)) && $questions === 'true' && $type != 'commission') {
  echo "<span style='background-color: rgb(100,100,255);'>&nbsp;</span>&nbsp;Questions orales&nbsp;&nbsp;";
}
echo "<span style='background-color: rgb(150,150,150);'>&nbsp;</span>&nbsp;Vacances parlementaires&nbsp;&nbsp;";
echo "<span style='font-weight: bolder; color: rgb(160,160,160);'>&mdash;</span>&nbsp;Présence médiane";
if ($link === 'true')
  echo '&nbsp;&nbsp;&nbsp;&nbsp;<a'.$target.' href="'.url_for('@faq', $abs).'#post_4">Explications</a></p>';
else echo '</p>';
}
if ($link != 'true')
  echo '</div>';
?>
