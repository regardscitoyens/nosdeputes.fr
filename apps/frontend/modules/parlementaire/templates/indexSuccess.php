<div class="temp">
<ul>
<li><?php echo link_to('tous les députés','@list_parlementaires')?></li>
<li><?php echo link_to('un député au hasard','@parlementaire_random')?></li>
<li><?php echo link_to('les députés par tag','@parlementaires_tags')?></li>
<li><?php echo link_to('tous les textes par interventions','@sections?order=plus')?></li>
<li><?php echo link_to('tous les textes ordre chrono','@sections?order=date')?></li>
<li><?php echo link_to('Synthèse globale','@top_global')?></li>
</ul>
<div>
<?php echo include_component('plot', 'groupes', array('plot' => 'total')); ?>
</div>
<div>
<?php echo include_component('tag', 'globalActivite'); ?>
</div>
</div>