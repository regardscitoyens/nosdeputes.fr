<?php
if (isset($rss)) echo include_component('parlementaire', 'header', array('parlementaire' => $parlementaire, 'titre' => $titre, 'rss' => '@parlementaire_interventions_rss?slug='.$parlementaire->slug));
else echo include_component('parlementaire', 'header', array('parlementaire' => $parlementaire, 'titre' => $titre));
?>
<?php  echo include_component('intervention', 'pagerInterventions', array('intervention_query' => $interventions, 'nophoto' => true)); ?>
