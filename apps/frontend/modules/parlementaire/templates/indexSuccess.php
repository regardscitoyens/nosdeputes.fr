<?php $sf_response->setTitle('NosDéputés.fr : Observatoire citoyen de l\'activité parlementaire'); ?>
<div class="clear">
<div class="separateur"></div>
<div class="accueil_message">
    <h1>Bienvenue sur NosDéputés.fr</h1>
    <p>Le site NosDéputés.fr vise à mettre en valeur l'activité des députés de l'Assemblée Nationale Française.</p>
<p>En synthétisant les activités des élus de la nation, il tente de donner aux citoyens de meilleurs clés pour comprendre et analyser ce que font leurs représentants.</p>
    <p>Ce site a été conçu comme une plateforme de médiation entre citoyens et députés. A travers des commentaires, les utilisateurs sont invités à exprimer leur point de vu et à partager leur expertise sur des points concrets de l'activité des députés.</p>
<p>Il est le fruit du travail du collectif <a href="htttp://www.regardscitoyens.org/">RegardsCitoyens.org</a></p>
</div>

	<div class="accueil_deputes_jour">
	<?php echo include_component('parlementaire', 'duJour'); ?>
	</div>
</div>
<div class="clear"></div>
<div class="separateur"></div>
<div class="clear accueil">
<div class="accueil_plot left">
	<div id="center">
	<?php echo include_component('plot', 'groupes', array('plot' => 'total')); ?>
	</div>
</div>

<div class="nuage_de_tags right">
	<h2>En ce moment à l'Assemblée Nationale</h2>
	<?php echo include_component('tag', 'globalActivite'); ?>
</div>
</div>