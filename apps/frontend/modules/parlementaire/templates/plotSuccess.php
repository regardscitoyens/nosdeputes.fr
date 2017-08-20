<?php
echo include_component('parlementaire', 'header', array('parlementaire' => $parlementaire, 'titre' => $title));
?>
<div class="par_session"><p>
<?php if ($session != $check)
  echo '<a href="'.url_for('@parlementaire_plot?slug='.$parlementaire->slug.'&time='.$check).'">';
  else echo '<b>';
  if ($fin) echo 'Toute la législature';
  else echo "Le$mois mois";
  if ($session != $check) echo '</a>';
  else echo '</b>';
  foreach ($sessions as $s) {
  echo ', ';
  if ($session != $s['session']) echo '<a href="'.url_for('@parlementaire_plot?slug='.$parlementaire->slug.'&time='.$s['session']).'">';
  else echo '<b>';
  echo 'la session '.preg_replace('/^(\d{4})/', '\\1-', $s['session']);
  if ($session != $s['session']) echo '</a>';
  else echo '</b>';
  } ?>
</p></div>

<?php echo include_component('plot', 'parlementaire', array('parlementaire' => $parlementaire, 'options' => array('plot' => 'all', 'questions' => 'true', 'session' => $session))); ?>
  <div class="explications" id="explications">
    <h2>Explications :</h2>
    <?php //echo link_to("Présence en réunions de commission et séances d'hémicycle",'@parlementaire_presences?slug='.$parlementaire->getSlug()); ?>
    <p class="indent_guillemets"><a href="/faq">voir les questions fréquentes (rubrique FAQ)</a></p>
  </div>
