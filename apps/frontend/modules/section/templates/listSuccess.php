<div class="travaux_parlementaires">
<h1>Les dossiers parlementaires</h1>
<?php include_component('section', 'simplifions', array()); ?>
<h2><?php echo $titre; ?></h2>
<ul>
<?php if ($order == 'date') { echo '<li>'; $mois = ""; }
foreach($sections as $s) if ($s->titre) {
  if (preg_match('/(questions?\s|ordre\sdu\sjour|nomination|suspension\sde\séance|rappels?\sau\srèglement)/i', $s->titre)) continue;
  $moisactuel = myTools::displayDateMoisAnnee($s->min_date);
  if ($order == 'date' && $mois != $moisactuel) {
    if ($mois != "") echo '</ul></li><li>';
    $mois = $moisactuel;
    echo '<h3>'.ucfirst($mois).'&nbsp;:</h3><ul>';
  }
  echo '<li>'.link_to(ucfirst($s->titre), '@section?id='.$s->id);
  echo ' ('.$s->nb_interventions.' intervention';
  if ($s->nb_interventions > 1) echo 's';
  if ($s->nb_commentaires > 0) echo ', '.$s->nb_commentaires.' commentaire';
  if ($s->nb_commentaires > 1) echo 's';
  if ($order == 'plus') echo ', '.$moisactuel;
  echo ')</li>';
 }
 if ($order == 'date') echo '</ul>'; ?>
 </ul></div>
