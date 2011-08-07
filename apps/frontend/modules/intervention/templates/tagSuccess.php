<?php 
$options = array('intervention_query' => $query, 'highlight' => $tags);
$titre = 'Interventions sur <em>"'.implode(', ', $tags).'"</em>';
if (isset($parlementaire)) {
  $sf_response->setTitle(strip_tags($titre).' de '.$parlementaire->nom);
  echo include_component('parlementaire', 'header', array('parlementaire' => $parlementaire, 'titre' => $titre));
  $options = array_merge($options, array('nophoto' => true)); 
} else {
  $sf_response->setTitle(strip_tags($titre)); ?>
<h1><?php echo $titre; ?></h1>
<?php } ?>
<?php echo include_component('intervention', 'pagerInterventions', $options); ?>

