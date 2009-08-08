<div class="temp">
<h1><?php echo link_to($section->titre, '@section?id='.$section->id); ?></h1>
<h2>Les interventions de <?php echo link_to($parlementaire->nom, '@parlementaire?slug='.$parlementaire->slug); ?></h2>
<?php
  echo include_component('intervention', 'pagerInterventions', array('intervention_query' => $qinterventions)); 
?>
</div>