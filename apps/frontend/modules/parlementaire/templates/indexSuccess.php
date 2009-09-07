<div class="temp">
<ul>
<li><?php echo link_to('tous les députés','@list_parlementaires')?></li>
<li><?php echo link_to('un député au hasard','@parlementaire_random')?></li>
<li><?php echo link_to('les députés par tag','@parlementaires_tags')?></li>
<li><?php echo link_to('tous les textes par interventions','@sections?order=plus')?></li>
<li><?php echo link_to('tous les textes ordre chrono','@sections?order=date')?></li>
<li><?php echo link_to('top des interventions','@top_interventions')?></li>
<li><?php echo link_to('top des présences','@top_presences')?></li>
<li><?php echo link_to('top des amendements','@top_amendements')?></li>
</ul>
</div>