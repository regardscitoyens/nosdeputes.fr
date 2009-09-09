<div class="temp">
<?php
$titre = "Interventions";
echo include_component('parlementaire', 'header', array('parlementaire' => $parlementaire, 'titre' => $titre));
?>
</div>
<div class="interventions">
<?php  echo include_component('intervention', 'pagerInterventions', array('intervention_query' => $interventions, 'nophoto' => true)); ?>
</div>
