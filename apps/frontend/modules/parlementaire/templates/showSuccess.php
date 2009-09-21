<?php $style = 'xneth'; # en attendant le style switcher ?>
<?php $top = $parlementaire->getTop();
      $sf_response->setTitle($parlementaire->nom); ?>
<div class="fiche_depute">
  <div class="info_depute">
<h1><?php echo $parlementaire->nom; ?>, <?php echo $parlementaire->getLongStatut(1); ?><span class="rss"><a href="<?php echo url_for('@parlementaire_rss?slug='.$parlementaire->slug); ?>"><?php echo image_tag($sf_request->getRelativeUrlRoot().'/images/'.$style.'/rss.png', 'alt="Flux rss"'); ?></a></span></h1>
    </div>
  <div class="depute_gauche">
    <div class="photo_depute">
    <?php echo '<img src="'.url_for('@resized_photo_parlementaire?height=160&slug='.$parlementaire->slug).'" class="photo_fiche" alt="Photo de '.$parlementaire->nom.'"/>'; ?>
    </div>
  </div>
  <div class="graph_depute">
      <?php echo include_component('plot', 'parlementaire', array('parlementaire' => $parlementaire, 'options' => array('plot' => 'total', 'questions' => 'on', 'link' => 'on'))); ?>
  </div>
  <div class="barre_activite">
 <?php include_partial('top', array('parlementaire'=>$parlementaire)); ?>
  </div>
  <div class="stopfloat"></div>
</div>

<div class="contenu_depute">
  <div class="boite_depute" id="b1">
    <div class="b_d_h"><div class="b_d_hg"></div><div class="b_d_hd"></div></div>
    <div class="b_d_cont">
      <div class="b_d_infos">
      <h2> Informations</h2>
    <ul>
<?php if ($parlementaire->fin_mandat) : ?>
      <li>Mandat clos rempli du <?php echo myTools::displayDate($parlementaire->debut_mandat); ?> au <?php echo myTools::displayDate($parlementaire->fin_mandat); ?></li>
<?php else : ?>
      <li>Mandat en cours depuis le <?php echo myTools::displayDate($parlementaire->debut_mandat); ?></li>
<?php endif; 
      if ($parlementaire->groupe_acronyme != "") : ?>
      <li>Groupe politique : <?php echo link_to(Organisme::getNomByAcro($parlementaire->groupe_acronyme), '@list_parlementaires_groupe?acro='.$parlementaire->groupe_acronyme); ?> (<?php echo $parlementaire->getgroupe()->getFonction(); ?>)</li>
      <?php endif; ?>
      <li>Profession : <?php if ($parlementaire->profession) : echo link_to($parlementaire->profession, '@list_parlementaires_profession?search='.$parlementaire->profession); else : ?>Non communiquée<?php endif; ?></li>
      <li><?php echo link_to('Fiche sur le site de l\'Assemblée Nationale', $parlementaire->url_an, array('title' => 'Lien externe')); ?></li>
      <li><a href="http://fr.wikipedia.org/wiki/<?php echo rawurlencode($parlementaire->nom); ?>">Page sur Wikipédia</a></li>
      <?php if ($parlementaire->site_web) : ?>
      <li><?php echo link_to('Site web', $parlementaire->site_web, array('title' => 'Lien externe')); ?></li>
      <?php endif; ?>  
    </ul>
    <?php if ($parlementaire->fin_mandat == null) : ?>
      <br /><h2>Responsabilités</h2>
      <ul>
        <li>Parlementaires :
          <ul>
            <?php foreach ($parlementaire->getResponsabilites() as $resp) { ?>
            <li><?php echo link_to($resp->getNom(), '@list_parlementaires_organisme?slug='.$resp->getSlug()); echo ' ('.$resp->getFonction().') '; ?></li>
            <?php } ?>
          </ul>
        </li>
        <?php if ($parlementaire->getExtras()) { ?>
        <li>Extra-parlementaires :
          <ul>
            <?php foreach ($parlementaire->getExtras() as $extra) { ?>
            <li><?php echo link_to($extra->getNom(),'@list_parlementaires_organisme?slug='.$extra->getSlug() ); ?> (<?php echo $extra->getFonction(); ?>)</li>
            <?php } ?>
          </ul>
        </li>
        <?php } ?>
      </ul>
      <?php endif; ?> <!-- else : ajouter les infos venant de parsing ancien (anciennes responsabilités) et avant les respon actuelles de ministre machin via les personnalites get fonctions? -->
      </div>
    </div>
    <div class="b_d_b"><div class="b_d_bg"></div><div class="b_d_bd"></div></div>
    <div class="b_d_h"><div class="b_d_hg"></div><div class="b_d_hd"></div></div>
    <div class="b_d_cont">
      <div class="b_d_infos">
      <h2>Questions au gouvernement</h2>
      <h3>Ses dernières questions orales</h3>
       <?php echo include_component('intervention', 'parlementaireQuestion', array('parlementaire' => $parlementaire, 'limit' => 5)); ?>
      <p class="suivant"><?php echo link_to('Toutes ses questions orales','@parlementaire_interventions?slug='.$parlementaire->getSlug().'&type=question'); ?></p>
      <h3>Ses dernières questions écrites</h3>
       <?php echo include_component('questions', 'parlementaire', array('parlementaire' => $parlementaire, 'limit' => 5)); ?>
      <p class="suivant"><?php echo link_to('Toutes ses questions écrites','@parlementaire_questions?slug='.$parlementaire->getSlug()); ?></p>
      </div>
    </div>
    <div class="b_d_b"><div class="b_d_bg"></div><div class="b_d_bd"></div></div>
  </div>
    
  <div class="boite_depute" id="b2">
    <div class="b_d_h"><div class="b_d_hg"></div><div class="b_d_hd"></div></div>
    <div class="b_d_cont">
      <div class="b_d_infos">
      <h2>Champ lexical</h2>
      <div style="text-align: justify">
<?php echo include_component('tag', 'parlementaire', array('parlementaire'=>$parlementaire)); ?>
<p class="suivant"><?php echo link_to('Tous ses mots', '@parlementaire_tags?slug='.$parlementaire->slug); ?></p>
      </div>
      </div>
    </div>
    <div class="b_d_b"><div class="b_d_bg"></div><div class="b_d_bd"></div></div>
  </div>
 
  <div class="boite_depute" id="b3">
    <div class="b_d_h"><div class="b_d_hg"></div><div class="b_d_hd"></div></div>
    <div class="b_d_cont">
      <div class="b_d_infos">
      <h2>Travaux législatifs</h2>
      <h3>Ses derniers dossiers</h3>
      <?php echo include_component('section', 'parlementaire', array('parlementaire' => $parlementaire, 'limit' => 5, 'textes' => $textes, 'order' => 'date')); ?>
      <p class="suivant"><?php echo link_to('Tous ses dossiers', '@parlementaire_textes?slug='.$parlementaire->slug); ?></p>
      <h3><?php echo link_to('Travaux en commissions','@parlementaire_interventions?slug='.$parlementaire->getSlug().'&type=commission'); ?></h3>
      <h3><?php echo link_to('Travaux en hémicycle','@parlementaire_interventions?slug='.$parlementaire->getSlug().'&type=loi'); ?></h3>
      <h3><?php echo link_to('Toutes ses interventions','@parlementaire_interventions?slug='.$parlementaire->getSlug().'&type=all'); ?></h3>
      <h3><?php echo link_to('Tous ses amendements','@parlementaire_amendements?slug='.$parlementaire->getSlug()); ?></h3>
      </div>
    </div>
    <div class="b_d_b"><div class="b_d_bg"></div><div class="b_d_bd"></div></div>
  </div>

  <div class="boite_depute" id="b4">
  </div>

  <div class="bas_depute">
      <h2>Derniers commentaires concernant <?php echo $parlementaire->nom; ?> <span class="rss"><a href="<?php echo url_for('@parlementaire_rss_commentaires?slug='.$parlementaire->slug); ?>"><?php echo image_tag($sf_request->getRelativeUrlRoot().'/images/'.$style.'/rss.png', 'alt="Flux rss"'); ?></a></span></h2>
      <?php echo include_component('commentaire', 'parlementaire', array('parlementaire' => $parlementaire)); ?>
      <p class="suivant"><?php echo link_to('Voir tous les commentaires', '@parlementaire_commentaires?slug='.$parlementaire->slug); ?></p>
    <div class="stopfloat"></div>
  </div>
</div>
