<div class="temp">
<?php
$i = new Intervention();
$i->setIntervention('test');
$i->addTag('test:test=1');
$i->save();
?><ul>
<li><?php echo link_to('tous les députés','@list_parlementaires')?></li>
<li><?php echo link_to('tous les textes','@sections')?></li>
<li><?php echo link_to('top des interventions','@top_interventions')?></li>
<li><?php echo link_to('top des présences','@top_presences')?></li>
</ul>
</div>