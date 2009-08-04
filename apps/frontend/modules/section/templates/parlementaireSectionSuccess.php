<h1><?php echo link_to($section->titre, '@section?id='.$section->id); ?></h1>
<h2>Les interventions de <?php echo link_to($parlementaire->nom, '@parlementaire?slug='.$parlementaire->slug); ?></h2>
<?php
foreach($interventions as $inter) 
  echo include_component('intervention', 'parlementaireIntervention', array('intervention' => $inter)); 
?>