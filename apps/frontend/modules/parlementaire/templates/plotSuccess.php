<?php
$surtitre = "Graphes d'activité parlementaire";
if ($session == 'lastyear') $titre = 'Sur les 12 derniers mois';
else $titre = 'Sur la session '.preg_replace('/^(\d{4})/', '\\1-', $session);
$sf_response->setTitle($surtitre.' de '.$parlementaire->nom.' '.strtolower($titre));
echo include_component('parlementaire', 'header', array('parlementaire' => $parlementaire, 'titre' => $surtitre));
?>
<div class="par_session"><p>
<?php if ($session != 'lastyear')
  echo '<a href="'.url_for('@parlementaire_plot?slug='.$parlementaire->slug.'&time=lastyear').'">';
  else echo '<b>';
  echo 'Les 12 derniers mois';
  if ($session != 'lastyear') echo '</a>';
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
    <?php //echo link_to("Présence en séances de commission et d'hémicycle",'@parlementaire_presences?slug='.$parlementaire->getSlug()); ?>
    <p class="indent_guillemets"><a href="/faq">voir les questions fréquentes (rubrique FAQ)</a></p>
  </div>
