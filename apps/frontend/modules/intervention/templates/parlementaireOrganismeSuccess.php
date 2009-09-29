<?php
echo include_component('parlementaire', 'header', array('parlementaire' => $parlementaire, 'titre' => $titre, 'surtitre' => $surtitre));
?>
<?php  echo include_component('intervention', 'pagerInterventions', array('intervention_query' => $interventions, 'nophoto' => true)); ?>
