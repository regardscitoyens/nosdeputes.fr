<?php
$histogram = false;
$abs = '';
if (!isset($target))
  $target = '';
if ($absolute)
  $abs = 'absolute=true';
$size='';
$width = 790;
$height = 300;
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
  echo '<a'.$target.' href="'.url_for('@parlementaire'.($absolute ? '' : '_plot').'?slug='.$parlementaire->slug.($absolute ? '' : '&time='.(myTools::isFinLegislature() ? 'legislature' : 'lastyear')), $abs).'">';
  if (!isset($widthrate)) $widthrate = 1;
  $height = floor($height / 2 *$widthrate);
  $width = floor($width * $widthrate);
} else echo '<div class="par_session">'; ?>
<div class="activity_plot" id="plot<?php echo $type; ?>">
  <?php if (!$absolute) echo '<noscript>'; ?>
  <img
    style="width: <?php echo $width; ?>px; height: <?php echo $height; ?>px;"
    alt="Participation <?php echo $titre; ?> de <?php echo $parlementaire->nom; ?>"
    src="<?php echo url_for('@parlementaire_plot_graph?slug='.$parlementaire->slug.'&time='.$time.'&type='.$type, $abs).'?questions='.$questions.'&link='.$link.'&histogram='.$histogram; ?>"
  />
  <?php if (!$absolute) echo '</noscript>'; ?>
</div>
<?php if ($link === 'true') echo '</a>';
if (!$absolute) : ?>
<script type="text/javascript">
plot_activity_data("<?php echo url_for('@parlementaire_plot_graph?slug='.$parlementaire->slug.'&time='.$time.'&type='.$type).'?questions='.$questions.'&format=json'; ?>", "plot<?php echo $type; ?>", "<?php echo $width; ?>", "<?php echo $height; ?>", "<?php echo $type; ?>", "<?php echo $histogram; ?>");
</script>
<?php endif;

$lela = ($parlementaire->sexe == "F" ? "la députée" : "le député");
$fem = ($parlementaire->sexe == "F" ? "e" : "");
if (!isset($widthrate) || $widthrate > 1/3) : ?>
<p><span class="jstitle" title="Nombre de <?php
$reus = "réunions de commissions auxquelles $lela a été enregistré$fem présent$fem";
$sean = "séances en hémicycle pendant lesquelles $lela est intervenu$fem même brièvement";
if ($type === "total") echo "$reus et de $sean";
else if ($type === "hemicycle") echo $sean;
else echo $reus;
?>"><span style="background-color: rgb(255,0,0);">&nbsp;</span>&nbsp;Présences <?php
echo ($type === 'commission' ? 'enregistr' : 'relev');
?>ées&nbsp;<span style="color: rgb(255,0,0);font-weight: bold;">*</span></span>&nbsp;&nbsp;&nbsp;<span class="jstitle" title="Nombre de <?php
$reus = "réunions de commissions";
$sean = "séances en hémicycle";
if ($type === "total") echo "$reus et de $sean";
else if ($type === "hemicycle") echo $sean;
else echo $reus; ?> pendant lesquelles <?php echo $lela; ?> a participé aux débats"><span style="background-color: rgb(255,200,0);">&nbsp;</span>&nbsp;Participations</span>&nbsp;&nbsp;&nbsp;<?php
if (!(myTools::isFinLegislature() && preg_match('/^l/', $time)) && $questions === 'true' && $type !== 'commission') :
?><span class="jstitle" title="Nombre de questions orales posées au gouvernement par <?php echo $lela; ?>"><span style="background-color: rgba(100,100,255,0.75);">&nbsp;</span>&nbsp;Questions orales</span>&nbsp;&nbsp;<?php
endif; ?><span class="jstitle" title="Semaines durant lesquelles les députés ne se sont réunis ni en commission ni en hémicycle"><span style="background-color: rgb(150,150,150);">&nbsp;</span>&nbsp;Vacances parlementaires</span>&nbsp;&nbsp;<span class="jstitle" title="Médiane pour l'ensemble des députés du nombre de <?php
if ($type === "total") echo "$reus et de $sean";
else if ($type === "hemicycle") echo $sean;
else echo $reus; ?> auxquelles ils ont participé"><span style="font-weight: bolder; color: rgb(160,160,160);">&mdash;</span>&nbsp;Présence médiane</span><?php
if ($link === 'true') : ?><?php
endif; ?></p>
<p><span style="color: rgb(255,0,0);">* : N'inclut pas toutes les présences en hémicycle mais uniquement celles avec intervention orale.</span><span>&nbsp;&nbsp;<a class="jstitle" title="Lire plus d'explications dans la FAQ"<?php echo $target; ?> href="<?php echo url_for('@faq', $abs); ?>#post_4">(plus d'explications)</a></span></p>
<?php endif;
if ($link != 'true')
  echo '</div>';
?>
