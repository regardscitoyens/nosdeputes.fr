<?php
if ($type == 'question') $titre = 'Questions orales';
else {
  $titre = 'Interventions';
  if ($type == 'loi') $titre .= ' en hémicycle';
  else if ($type == 'commission') $titre .= ' en commissions';
}
echo include_component('parlementaire', 'header', array('parlementaire' => $parlementaire, 'titre' => $titre));
?>
<?php  echo include_component('intervention', 'pagerInterventions', array('intervention_query' => $interventions, 'nophoto' => true)); ?>
