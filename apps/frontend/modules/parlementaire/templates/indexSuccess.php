<?php $sf_response->setTitle('NosDéputés.fr : Observatoire citoyen de l\'activité parlementaire'); ?>
<div class="texte_presentation_gauche">
	<h1>Bienvenue</h1>
	<span id="illustration">
	</span>
	<div id="texte_a_ecrire">
	Texte texte Texte t exte Texte tex te Texte   texte Texte t exte T exte texte Text e texte Texte texte Texte texte 
	Texte texte Te xte texte Te xte texte Texte texte Texte texte Texte texte Texte texte Texte texte Texte texte
	Tex te t ex te Texte texte Texte  texte Te xte texte T exte texte Text e te xte Text e texte Text e te te Texte texte 
	Te xte tex te  Texte  texte Texte tex te Texte texte T exte texte Texte  texte Texte texte  Texte tex te Texte texte 
	Texte tex te Texte texte Texte text e Texte texte Text e texte Tex te texte Texte te xte Texte tex te Texte texte 
	Texte texte Texte t exte Texte tex te Texte   texte Texte t exte T exte texte Text e texte Texte texte Texte texte 
	Texte texte Te xte texte Te xte texte Texte texte Texte texte Texte texte Texte texte Texte texte Texte texte
	Tex te t ex te Texte texte Texte  texte Te xte texte T exte texte Text e te xte Text e texte Text e te te Texte texte 
	Te xte tex te  Texte  texte Texte tex te Texte texte T exte texte Texte  texte Texte texte  Texte tex te Texte texte 
	Texte tex te Texte texte Texte text e Texte texte Text e texte Tex te texte Texte te xte Texte tex te Texte texte 
	</div>
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
	<h2>Mots clés les plus prononcés :</h2>
	<?php echo include_component('tag', 'globalActivite'); ?>
</div>