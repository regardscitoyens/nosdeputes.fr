<?php $style = 'fixe'; # en attendant le style switcher ?>
<?php $top = $parlementaire->getTop() ?>
<div class="fiche_depute">
  <div class="info_depute">
<p><h1><b><?php echo $parlementaire->nom; ?></b>, <?php echo $parlementaire->getLongStatut(1); ?></h1></p>
    </div>
  <div class="depute_gauche">
    <div class="photo_depute">
<?php    echo '<img src="'.url_for('@resized_photo_parlementaire?height=160&slug='.$parlementaire->slug).'" class="photo_fiche" alt="Photo de '.$parlementaire->nom.'"/>'; ?>
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
		<div class="meilleur_pire">
		</div>
		<br /><h3> Informations :</h3>
    <ul>
<?php if ($parlementaire->groupe_acronyme != "") : ?>
      <li>Groupe politique : <?php echo link_to(Organisme::getNomByAcro($parlementaire->groupe_acronyme), '@list_parlementaires_groupe?acro='.$parlementaire->groupe_acronyme); ?> (<?php echo $parlementaire->getgroupe()->getFonction(); ?>)</li>
<?php endif; ?>
      <li>Profession : <?php if ($parlementaire->profession) : echo link_to($parlementaire->profession, '@list_parlementaires_profession?search='.$parlementaire->profession); else : ?>Non communiquée<?php endif; ?></li>
      <li><?php echo link_to('Fiche sur le site de l\'Assemblée Nationale', $parlementaire->url_an, array('title' => 'Lien externe', 'target' => '_blank')); ?></li>
      <li><a href="http://fr.wikipedia.org/wiki/<?php echo $parlementaire->nom; ?>">Page sur Wikipédia</a></li>
      <?php if ($parlementaire->site_web) : ?>
      <li><?php echo link_to('Site web', $parlementaire->site_web, array('title' => 'Lien externe', 'target' => '_blank')); ?></li>
      <?php endif; ?>  
    </ul>
    <?php if ($parlementaire->fin_mandat == null) : ?>
      <br /><h3>Responsabilités</h3>
      <ul>
        <li>Parlementaires :
          <ul>
            <?php foreach ($parlementaire->getResponsabilites() as $resp) { ?>
            <li><?php echo link_to($resp->getNom(), '@list_parlementaires_organisme?slug='.$resp->getSlug()); echo ' ('.$resp->getFonction().') '.link_to('-> interventions', '@parlementaire_interventions_organisme?slug='.$parlementaire->slug.'&orga='.$resp->organisme_id); ?></li>
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
          <?php } ?>
        </li>
      </ul>
      <?php endif; ?> <!-- else : ajouter les infos venant de parsing ancien (anciennes responsabilités) et avant les respon actuelles de ministre machin via les personnalites get fonctions? -->
      <div class ="adresses">
      <h3>Adresses</h3>
        <div class="tab_adresse" id="tab_adresse_1">
        <h4>Adresse 1</h4>
        <p>12 Boulevard Machin<br />
        92100 Machin sur Truc<br />
        Téléphone : <a href="callto:0033549021575">05 49 02 15 75</a><br />
        Télécopie : 05 49 02 15 76
        </p>
        </div>
        <div class="tab_adresse" id="tab_adresse_2">
        <h4>Adresse 2</h4>
        <p>10 Rue du truc<br />
        21100 Truc sur Machin<br />
        Téléphone : <a href="callto:0033549021575">05 49 02 15 75</a><br />
        Télécopie : 05 49 02 15 76
        </p>
        </div>
        <div class="tab_adresse" id="tab_adresse_3">
        <h4>Permanence parlementaire</h4>
        <p>102 Boulevard Blossac<br />
        86100 Châtellerault<br />
        Téléphone : <a href="callto:0033549021575">05 49 02 15 75</a><br />
        Télécopie : 05 49 02 15 76
        </p>
        </div>
      </div>
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
<p><?php echo link_to('Tous les mots', '@parlementaire_tags?slug='.$parlementaire->slug); ?></p>
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
      <p><?php echo link_to('Tous ses dossiers', '@parlementaire_textes?slug='.$parlementaire->slug); ?></p>
      <h3><?php echo link_to('Travaux en commissions','@parlementaire_interventions?slug='.$parlementaire->getSlug().'&type=commission'); ?></h3>
      <h3><?php echo link_to('Travaux en hémicycle','@parlementaire_interventions?slug='.$parlementaire->getSlug().'&type=loi'); ?></h3>
      <h3><?php echo link_to('Toutes ses interventions','@parlementaire_interventions?slug='.$parlementaire->getSlug().'&type=all'); ?></h3>
      <h3><?php echo link_to('Tous ses amendements','@parlementaire_amendements?slug='.$parlementaire->getSlug()); ?></h3>
      </div>
    </div>
    <div class="b_d_b"><div class="b_d_bg"></div><div class="b_d_bd"></div></div>
  </div>

  <div class="boite_depute" id="b4">
    <div class="b_d_h"><div class="b_d_hg"></div><div class="b_d_hd"></div></div>
    <div class="b_d_cont">
      <div class="b_d_infos">
      <h2>Questions au gouvernement</h2>
      <h3>Ses dernières questions orales</h3>
       <?php echo include_component('intervention', 'parlementaireQuestion', array('parlementaire' => $parlementaire, 'limit' => 5)); ?>
      <p><?php echo link_to('Toutes ses questions orales','@parlementaire_interventions?slug='.$parlementaire->getSlug().'&type=question'); ?></p>
      <h3>Ses dernières questions écrites</h3>
       <?php echo include_component('questions', 'parlementaire', array('parlementaire' => $parlementaire, 'limit' => 5, 'questions' => $questions, 'order' => 'date')); ?>
      <p><?php echo link_to('Toutes ses questions écrites','@parlementaire_questions?slug='.$parlementaire->getSlug()); ?></p>
      </div>
    </div>
    <div class="b_d_b"><div class="b_d_bg"></div><div class="b_d_bd"></div></div>
  </div>

  <div class="bas_depute">
    <div class="bas_depute_g">
      <!-- 
      <h2>Derniers commentaires</h2>  
      <?php # var_dump( sfConfig::get('sf_escaping_strategy') ); ?>
       <div class="boite_citoyen">
        <div class="b_c_h"><div class="b_c_hg"></div><div class="b_c_hd"></div></div>
        <div class="b_c_cont">
          <div class="b_c_photo">
          
          </div>
          <div class="b_c_text">
            <h3>Jojo C. <span class="note"><?php echo image_tag('../css/'.$style.'/images/etoile.png', 'alt="***"'); ?></span></h3>
            <p><a href="#">23 articles</a></p>
            <p><a href="#">Voir la fiche perso</a></p>
          </div>
        </div>
        <div class="b_c_b"><div class="b_c_bg"></div><div class="b_c_bd"></div></div>
      </div> -->
    </div>
    <div class="bas_depute_d">
      <h2>Derniers commentaire concernant <a href="#"><?php echo $parlementaire->slug; ?></a> <span class="rss"><a href="#"><?php echo image_tag('../css/'.$style.'/images/rss.png', 'alt="Flux rss"'); ?></a></span></h2>
      <?php echo include_component('commentaire', 'parlementaire', array('parlementaire' => $parlementaire)); ?>
      <?php echo link_to('Voir tous les commentaires', '@parlementaire_commentaires?slug='.$parlementaire->slug); ?>
    </div>
    <div class="stopfloat"></div>
  </div>
</div>
