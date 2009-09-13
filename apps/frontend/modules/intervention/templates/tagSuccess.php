<?php 
$options = array('intervention_query' => $query, 'highlight' => $tags);
if (isset($parlementaire)) {
$titre = 'Interventions sur <em>"'.implode(', ', $tags).'"</em>';
echo include_component('parlementaire', 'header', array('parlementaire' => $parlementaire, 'titre' => $titre));
$options = array_merge($options, array('nophoto' => true)); 
} ?>
<?php echo include_component('intervention', 'pagerInterventions', $options); ?>

