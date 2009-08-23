<div class="titre_int_et_seance">
<h1>Les interventions de <?php 
echo $parlementaire->nom.' '; 
if ($parlementaire->hasPhoto()) { 
  echo '<img src="'.url_for('@resized_photo_parlementaire?height=150&slug='.$parlementaire->slug).'" alt="Photo de '.$parlementaire->nom.'" />'; 
} ?></h1>

</div>
<div class="interventions">
<?php  echo include_component('intervention', 'pagerInterventions', array('intervention_query' => $interventions)); ?>
</div>