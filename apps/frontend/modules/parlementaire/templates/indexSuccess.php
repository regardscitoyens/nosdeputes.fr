<?php $sf_response->setTitle('NosDéputés.fr : Observatoire citoyen de l\'activité parlementaire');  $style = "xneth"; ?>
<script type="text/javascript">
// preload img fond sous-menu
$('<img />').attr('src', '<?php echo $sf_request->getRelativeUrlRoot()."/css/".$style."/images/sous_menu_combined.png"; ?>');
</script>
<div class="clear">
<div class ="accueil_message">
<div class="accueil_message_content">
    <h1>Bienvenue sur NosDéputés.fr</h1>
    <p>NosDéputés.fr est un site qui cherche à mettre en valeur l'activité parlementaire des députés de l'Assemblée nationale Française. En synthétisant les différentes activités législatives et de contrôle du gouvernement des élus de la nation, ce site essaie de donner aux citoyens de nouveaux outils pour comprendre et analyser le travail de leurs représentants.</p>
    <p>Conçu comme une plateforme de médiation entre citoyens et députés, le site propose à chacun de participer et de s'exprimer sur les débats parlementaires. Au travers de leurs commentaires, les utilisateurs sont invités à créer le débat en partageant leur expertise lorsque cela leur semble utile. Peut-être pourront-ils ainsi nourrir le travail de leurs élus ?</p>
    <p>Vous pouvez consulter l'activité de leurs collègues du <a href="http://nossenateurs.fr/">Sénat</a> sur notre autre initiative <a href="http://www.NosSenateurs.fr/">Nos Sénateurs</a>.</p>
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
<div class="clear accueil">
  <div class="box_news">
  <div class="carte" id="cartedeputes">
  <h2><span style="margin-right: 5px;"><img alt="actu" src="<?php echo $sf_request->getRelativeUrlRoot(); ?>/images/xneth/ico_nosdeputes.png" /></span>Trouver son député</h2>
    <div class="cont_box_news">
	  <?php include_partial('circonscription/mapDepartement', array('width'=>0, 'height'=>260, 'link' => true)); ?>
	  </div>
	  <div class="message">
	  <p>Pour retrouver votre député sur le site, vous pouvez saisir son nom.</p>
          <p>Si vous ne le connaissez pas, indiquez votre code postal ou le nom<br/>de votre commune, et nous essaierons de le trouver pour vous&nbsp;:</p>
	  <form action="<?php echo url_for('solr/search?object_name=Parlementaire'); ?>">
	  <input size="25" name="search"/><input type="hidden" name="object_name" value="Parlementaire"/><input type="submit" value="Trouver mon député"/>
          <div><small><em>Exemples : patrick, 77840, saint-herblain, trois rivières, ...</em></small></div>
	  </form>
	  </div>
    </div>
  </div>
  <div class="clear"></div>
  <div class="box_container">
    <?php echo include_component('tag', 'globalActivite'); ?>
    <?php echo include_component('plot', 'syntheseGroupes', array('type' => 'home')); ?>
  </div>
  <div class="clear"></div>
  <?php include_component('commentaire', 'homeWidget', array('type' => 'home')); ?>
</div>
