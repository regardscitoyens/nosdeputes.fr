<?php $top = $parlementaire->getTop();
      $sf_response->setTitle($parlementaire->nom.' - Son activité de député à l\'Assemblée nationale - NosDéputés.fr'); ?>
<div class="fiche_depute">
  <div class="info_depute">
<h1><?php echo $parlementaire->nom; ?></h1><h2>, <?php echo $parlementaire->getLongStatut(1); ?><span class="rss"><a href="<?php echo url_for('@parlementaire_rss?slug='.$parlementaire->slug); ?>"><?php echo image_tag('xneth/rss.png', 'alt="Flux rss"'); ?></a></span></h2>
    </div>
  <div class="depute_gauche">
    <div class="photo_depute">
	<?php include_partial('parlementaire/photoParlementaire', array('parlementaire' => $parlementaire, 'height' => 160)); ?>
    </div>
  </div>
  <div class="graph_depute">
      <?php echo include_component('plot', 'parlementaire', array('parlementaire' => $parlementaire, 'options' => array('plot' => 'total', 'questions' => 'true', 'link' => 'true'))); ?>
  </div>
  <div class="barre_activite">
 <?php include_partial('top', array('parlementaire'=>$parlementaire)); ?>
  </div>
  <div class="stopfloat"></div>
</div>

<div class="contenu_depute">
	<?php include_partial('parlementaire/fiche', array('parlementaire'=>$parlementaire, 'commissions_permanentes' => $commissions_permanentes, 'missions' => $missions)); ?>
  <div class="bas_depute">
      <h2 class="list_com">Derniers commentaires concernant <?php echo $parlementaire->nom; ?> <span class="rss"><a href="<?php echo url_for('@parlementaire_rss_commentaires?slug='.$parlementaire->slug); ?>"><?php echo image_tag('xneth/rss.png', 'alt="Flux rss"'); ?></a></span></h2>
      <?php if ($parlementaire->nb_commentaires == 0) echo '<p>Le travail de ce député n\'a pas encore inspiré de commentaire aux utilisateurs.</p>';
        else {
          echo include_component('commentaire', 'lastObject', array('object' => $parlementaire, 'presentation' => 'noauteur'));
          if ($parlementaire->nb_commentaires > 4)
            echo '<p class="suivant list_com">'.link_to('Voir les '.$parlementaire->nb_commentaires.' commentaires', '@parlementaire_commentaires?slug='.$parlementaire->slug).'</p><div class="stopfloat"></div>'; ?>
     <?php } ?>
  </div>
</div>
