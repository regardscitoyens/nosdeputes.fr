<div class="titre_int_et_seance">
<h1>Les interventions de <?php 
echo $parlementaire->nom.' '; 
if ($parlementaire->getPhoto()) { 
  echo image_tag($parlementaire->getPhoto(), ' alt=Photo de '.$parlementaire->nom); 
} ?></h1>

</div>
<div class="interventions">
<?php 
foreach ($parlementaire->getInterventions() as $intervention) {
  echo include_component('intervention', 'parlementaireIntervention', array('intervention' => $intervention));
}?>
</div>