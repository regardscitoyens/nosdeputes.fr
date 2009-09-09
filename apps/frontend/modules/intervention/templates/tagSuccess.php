<div class="temp">
<?php
$titre = 'Interventions sur <em>"'.implode(', ', $tags).'"</em>';
echo include_component('parlementaire', 'header', array('parlementaire' => $parlementaire, 'titre' => $titre));
?>
<?php echo include_component('intervention', 'pagerInterventions', array('intervention_query' => $query, 'highlight' => $tags, 'nophoto' => true)); ?>
</div>