<h1>Recherche d'interventions portant sur <i><?php echo $mots; $sf_response->setTitle('Recherche d\'interventions portant sur '.$mots); ?></i></h1>
<?php
echo include_component('intervention', 'pagerInterventions', array('intervention_query' => $query, 'highlight' => $high, 'mots' => $mots));
?>
