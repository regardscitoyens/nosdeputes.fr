<?php $sf_response->setTitle('NosDéputés.fr : Observatoire citoyen de l\'activité parlementaire'); ?>
<div class="clear">
<div class="separateur"></div>
<div class ="accueil_message">
<div class="accueil_message_content">
    <h1>Bienvenue sur NosDéputés.fr</h1>
    <p>NosDéputés.fr est un site qui cherche à mettre en valeur l'activité parlementaire des députés de l'Assemblée Nationale Française.</p>
    <p>En synthétisant les différentes activités législatives et de contrôle du gouvernement des élus de la nation, ce site essaie de donner aux citoyens de nouveaux outils pour comprendre et analyser le travail de leurs représentants.</p>
    <p>Conçu comme une plateforme de médiation entre citoyens et députés, le site propose à chacun de participer et de s'exprimer sur les débats parlementaires. Au travers de leurs commentaires, les utilisateurs sont invités à créer le débat en partageant leur expertise lorsque cela leur semble utile. Peut-être pourront-ils ainsi nourrir le travail de leurs élus ?</p>
  </div>
  <div class="accueil_message_signature">
    <p>Toute l'équipe du collectif <a href="http://www.regardscitoyens.org/">RegardsCitoyens.org</a>.</p>
  </div>
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
	<h2><?php echo link_to('En ce moment à l\'Assemblée Nationale', '@parlementaires_tags'); ?></h2>
	<?php echo include_component('tag', 'globalActivite'); ?>
</div>
</div>
