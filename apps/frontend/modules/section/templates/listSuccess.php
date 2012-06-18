<div class="travaux_parlementaires">
<h1>Les dossiers parlementaires</h1>
<?php include_component('section', 'simplifions', array()); ?>
<h2><?php echo $titre; ?></h2>
<ul>
<?php if ($order == 'date') { if (count($sections)) echo '<li>'; $mois = ""; }
foreach($sections as $s) if ($s->titre) {
  if (preg_match('/(questions?\s|ordre\sdu\sjour|nomination|suspension\sde\séance|rappels?\sau\srèglement)/i', $s->titre)) continue;
  $moisactuel = myTools::displayDateMoisAnnee($s->max_date);
  if ($order == 'date' && $mois != $moisactuel) {
    if ($mois != "") echo '</ul></li><li>';
    $mois = $moisactuel;
    echo '<h3>'.ucfirst($mois).'&nbsp;:</h3><ul>';
  }
  echo '<li>'.link_to(ucfirst($s->titre), '@section?id='.$s->id);
  if ($order != 'date' || $s->nb_interventions > 0 || $s->nb_commentaires)
    echo ' (';
  if ($s->nb_interventions > 0)
    echo '<span class="list_inter">'.$s->nb_interventions.'&nbsp;intervention';
  if ($s->nb_interventions > 1)
    echo 's';
  if ($s->nb_interventions > 0)
    echo '</span>';
  if ($s->nb_interventions > 0 && $s->nb_commentaires)
    echo ', ';
  if ($s->nb_commentaires > 0)
    echo '<span class="list_com">'.$s->nb_commentaires.'&nbsp;commentaire';
  if ($s->nb_commentaires > 1)
    echo 's';
  if ($s->nb_commentaires > 0)
    echo '</span>';
  if ($order != 'date' && ($s->nb_interventions > 0 || $s->nb_commentaires > 0))
    echo ', ';
  if ($order != 'date')
    echo $moisactuel;
  if ($order != 'date' || $s->nb_interventions > 0 || $s->nb_commentaires)
    echo ')';
  echo '</li>';
}
if ($order == 'date') echo '</ul>'; ?>
</ul></div>
