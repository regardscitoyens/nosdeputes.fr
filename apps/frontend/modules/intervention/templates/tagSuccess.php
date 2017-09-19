<?php
$options = array('intervention_query' => $query, 'highlight' => $tags);
if (isset($parlementaire)) {
  echo include_component('parlementaire', 'header', array('parlementaire' => $parlementaire, 'titre' => $titre));
  $options = array_merge($options, array('nophoto' => true));
} else { ?>
<h1><?php echo $titre; ?></h1>
<?php } ?>
<?php echo include_component('intervention', 'pagerInterventions', $options); ?>

