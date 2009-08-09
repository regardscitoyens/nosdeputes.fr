<div class="titre_amdmts_perso">
<h1>Les amendements de <?php
echo $parlementaire->nom.' '; 
if ($parlementaire->getPhoto()) { 
  echo image_tag($parlementaire->getPhoto(), ' alt=Photo de '.$parlementaire->nom); 
} ?></h1>

</div>
<div class="amendements">
<?php  echo include_component('amendement', 'pagerAmendements', array('amendement_query' => $amendements)); ?>
</div>