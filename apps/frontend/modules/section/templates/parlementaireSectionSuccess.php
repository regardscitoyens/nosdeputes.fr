<div class="temp">
<?php if ($section->getSection()) echo '<h1>'.link_to($section->getSection()->getTitre(), '@section?id='.$section->section_id).'</h1>';
echo '<h2>'.link_to($section->titre, '@section?id='.$section->id).'</h2>'; ?>
<h2>Les interventions de <?php echo link_to($parlementaire->nom, '@parlementaire?slug='.$parlementaire->slug); ?></h2>
<?php
  echo include_component('intervention', 'pagerInterventions', array('intervention_query' => $qinterventions)); 
?>
</div>