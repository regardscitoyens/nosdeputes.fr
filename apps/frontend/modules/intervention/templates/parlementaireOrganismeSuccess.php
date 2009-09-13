<?php
  $surtitre = link_to($orga->getNom(), '@list_parlementaires_organisme?slug='.$orga->getSlug());
  $titre = 'Interventions';
echo include_component('parlementaire', 'header', array('parlementaire' => $parlementaire, 'titre' => $titre, 'surtitre' => $surtitre));
?>
<?php  echo include_component('intervention', 'pagerInterventions', array('intervention_query' => $interventions, 'nophoto' => true)); ?>
