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
$PictureID = "Map_".rand(1,10000).".map";
?>

<?php if ($link === 'true') echo '<a href="'.url_for('@parlementaire_plot?slug='.$parlementaire->slug.'&time=lastyear').'">';
else echo '<div class="par_session">'; ?>
<img id="graph<?php echo $PictureID; ?>" alt="Participation <?php echo $titre; ?> de <?php echo $parlementaire->nom; ?>" src="<?php echo url_for('@parlementaire_plot_graph?slug='.$parlementaire->slug.'&time='.$time.'&type='.$type).'?questions='.$questions.'&link='.$link.'&mapId='.$PictureID; ?>" onmousemove="getMousePosition(event);" onmouseout="nd();"/>
<?php if ($link === 'true') { ?>
<script type="text/javascript">
<!--
LoadImageMap("graph<?php echo $PictureID; ?>", "<?php echo url_for('@parlementaire_plot_graph?slug='.$parlementaire->slug.'&time='.$time.'&type='.$type).'?questions='.$questions.'&link='.$link.'&drawAction=map&mapId='.$PictureID; ?>");
//-->
</script>
<?php } ?>

<?php if ($link === 'true') echo '</a>';
$lela = ($parlementaire->sexe == "F" ? "la sénatrice" : "le sénateur");
$fem = ($parlementaire->sexe == "F" ? "e" : ""); ?>
<p>
<span class="jstitle" title="Nombre de <?php
$reus = "réunions de commissions auxquelles $lela a été enregistré$fem présent$fem";
$sean = "séances en hémicycle pendant lesquelles $lela est intervenu$fem même brièvement";
if ($type === "total") echo "$reus et de $sean";
else if ($type === "hemicycle") echo $sean;
else echo $reus;
?>"><span style="background-color: rgb(255,0,0);">&nbsp;</span>&nbsp;Présences <?php echo ($type === 'commission' ? 'enregistr' : 'détect');
?>ées</span>&nbsp;&nbsp;&nbsp;
<span class="jstitle" title="Nombre de <?php
$reus = "réunions de commissions";
$sean = "séances en hémicycle";
if ($type === "total") echo "$reus et de $sean";
else if ($type === "hemicycle") echo $sean;
else echo $reus; ?> pendant lesquelles <?php echo $lela; ?> a participé aux débats"><span style="background-color: rgb(255,200,0);">&nbsp;</span>&nbsp;Participations</span>&nbsp;&nbsp;&nbsp;
<span class="jstitle" title="Nombre de dizaines de milliers de mots prononcés par <?php echo $lela; ?> au cours de ses interventions"><span style="background-color: rgb(0,255,0);">&nbsp;</span>&nbsp;Mots prononcés (x&nbsp;10&nbsp;000)</span>&nbsp;&nbsp;&nbsp;
<?php if ($questions === 'true' && $type !== 'commission') : ?><span class="jstitle" title="Nombre de questions au gouvernement posées en séance par <?php echo $lela; ?>"><span style="background-color: rgba(100,100,255,0.75);">&nbsp;</span>&nbsp;Questions orales</span>&nbsp;&nbsp;<?php endif; ?>
<span class="jstitle" title="Semaines durant lesquelles les sénateurs ne se sont réunis ni en commission ni en hémicycle"><span style="background-color: rgb(150,150,150);">&nbsp;</span>&nbsp;Vacances parlementaires</span>&nbsp;&nbsp;
<?php if ($link === 'true') : ?><span>&nbsp;&nbsp;&nbsp;&nbsp;<a class="jstitle graphe_explications" title="Consultez les questions fréquentes pour plus d'explications" href="<?php echo url_for('@faq'); ?>#post_1">Lire plus d'explications</a></span><?php endif; ?>
</p>
<?php if ($link != 'true') echo '</div>'; ?>
