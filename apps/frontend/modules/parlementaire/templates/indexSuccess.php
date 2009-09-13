<div class="temp">
<div class="texte_presentation_gauche">
</div>

<div class="menu_accueil_droite">
	<div class="accueil_liste_liens">
	<h2>Navigation :</h2>
	<ul>
	<li id="couleur1"><?php echo link_to('Tous les députés','@list_parlementaires')?></li>
	<li id="couleur2"><?php echo link_to('Un député au hasard','@parlementaire_random')?></li>
	<li id="couleur1"><?php echo link_to('Les députés par tag','@parlementaires_tags')?></li>
	<li id="couleur2"><?php echo link_to('Tous les textes par interventions','@sections?order=plus')?></li>
	<li id="couleur1"><?php echo link_to('Tous les textes ordre chrono','@sections?order=date')?></li>
	<li id="couleur2"><?php echo link_to('Synthèse globale','@top_global')?></li>
	</ul>
	</div>
	<div class="accueil_deputes_jour">
	<?php echo include_component('parlementaire', 'duJour'); ?>
	</div>
</div>

<div class="accueil_plot">
	<div id="center">
	<?php echo include_component('plot', 'groupes', array('plot' => 'total')); ?>
	</div>
</div>

<div class="accueil_nuage_tags">
	<?php echo include_component('tag', 'globalActivite'); ?>
</div>
</div>