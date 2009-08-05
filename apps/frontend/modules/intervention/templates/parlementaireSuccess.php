<div class="titre_int_et_seance">
<h1>Les interventions de <?php 
echo $parlementaire->nom.' '; 
if ($parlementaire->getPhoto()) { 
  echo image_tag($parlementaire->getPhoto(), ' alt=Photo de '.$parlementaire->nom); 
} ?></h1>

</div>
<div class="interventions">
<?php  echo include_component('intervention', 'pagerInterventions', array('intervention_query' => $interventions)); ?>
</div>