<?php $style = 'fixe'; # en attendant le style switcher ?>
<div class="fiche_depute">
  <div class="depute_gauche">
    <div class="photo_depute">
<?php    echo '<img src="'.url_for('@resized_photo_parlementaire?height=150&slug='.$parlementaire->slug).'" class="photo_fiche" alt="Photo de '.$parlementaire->nom.'"/>'; ?>
    </div>
  </div>
  <div class="graph_depute">
    <div class="info_depute">
      <h1><?php echo $parlementaire->nom.', '.$parlementaire->getLongStatut(1); ?></h1>
    </div>
      <?php echo include_component('plot', 'parlementairePresenceLastYear', array('parlementaire' => $parlementaire, 'options' => array('plot' => 'total', 'questions' => 'on', 'link' => 'on'))); ?>
  </div>
  <div class="barre_activite">
    <h2>Activité parlementaire : </h2>
    <ul>
      <li title="Interventions en séance"><a href="#"><?php echo image_tag('../css/'.$style.'/images/seance.png', 'alt="Interventions en séance"'); ?> : 7</a></li>
      <li title="Interventions en commissions"><a href="#"><?php echo image_tag('../css/'.$style.'/images/rapport.png', 'alt="Interventions en commissions"'); ?> : 2</a></li>
      <li title="Rapports"><a href="#"><?php echo image_tag('../css/'.$style.'/images/rapport.png', 'alt="Rapports"'); ?> : 2</a></li>
      <li title="Propositions de loi (auteur)"><a href="#"><?php echo image_tag('../css/'.$style.'/images/balance.png', 'alt="Propositions de loi (auteur)"'); ?> : 0</a></li>
      <li title="Questions"><a href="#"><?php echo image_tag('../css/'.$style.'/images/question.png', 'alt="Questions"'); ?>   : 50</a></li>
      <li><span class="barre_date"><?php if ($parlementaire->fin_mandat == null) echo "Depuis le"; else echo "Mandat terminé"; ?>&nbsp;: <?php echo $parlementaire->debut_mandat; if ($parlementaire->fin_mandat != null) echo " - ".$parlementaire->fin_mandat; ?></span></li>
    </ul>
  </div>
  <div class="stopfloat"></div>
</div>

<div class="contenu_depute">
  <div class="boite_depute" id="b1">
    <div class="b_d_h"><div class="b_d_hg"></div><div class="b_d_hd"></div></div>
    <div class="b_d_cont">
      <div class="b_d_infos">
    <p>Né le ... (... ans) à ... (...)</p>
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
      <h3>Responsabilités</h3>
      <ul>
        <li>Parlementaires :
          <ul>
            <?php foreach ($parlementaire->getResponsabilites() as $resp) { ?>
            <li><?php echo link_to($resp->getNom(), '@list_parlementaires_organisme?slug='.$resp->getSlug()); echo ' ('.$resp->getFonction().')'; ?></li>
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
      <p style="margin-top:130px;">Source : <a href="<?php echo $parlementaire->url_an; ?>" target='_blank'>Assemblée Nationale</a></p>
      </div>
    </div>
    <div class="b_d_b"><div class="b_d_bg"></div><div class="b_d_bd"></div></div>
  </div>
    
  <div class="boite_depute" id="b2">
    <div class="b_d_h"><div class="b_d_hg"></div><div class="b_d_hd"></div></div>
    <div class="b_d_cont">
      <div class="b_d_infos">
      <h2>Implication dans un projet de loi</h2>
<?php echo include_component('section', 'parlementaire', array('parlementaire' => $parlementaire, 'limit'=>5, 'textes' => $textes)); ?>
<?php echo link_to('suite', '@parlementaire_textes?slug='.$parlementaire->slug); ?>
      </div>
    </div>
    <div class="b_d_b"><div class="b_d_bg"></div><div class="b_d_bd"></div></div>
  </div>
  
  <div class="boite_depute" id="b3">
    <div class="b_d_h"><div class="b_d_hg"></div><div class="b_d_hd"></div></div>
    <div class="b_d_cont">
      <div class="b_d_infos">
      <h2>Questions écrites/orales</h2>
      </div>
    </div>
    <div class="b_d_b"><div class="b_d_bg"></div><div class="b_d_bd"></div></div>
  </div>
  
  <div class="boite_depute" id="b4">
    <div class="b_d_h"><div class="b_d_hg"></div><div class="b_d_hd"></div></div>
    <div class="b_d_cont">
      <div class="b_d_infos">
      <h2>Interventions</h2>
      <h3><?php echo link_to("Présence en séances de commission et d'hémicycle",'@parlementaire_presences?slug='.$parlementaire->getSlug()); ?></h3>
      <h3><?php echo link_to("Toutes ses interventions",'@parlementaire_interventions?slug='.$parlementaire->getSlug()); ?></h3>
      <h3><?php echo link_to("Tous ses amendements",'@parlementaire_amendements?slug='.$parlementaire->getSlug()); ?></h3>
      <h3><?php echo link_to("Toute ses questions écrites",'@parlementaire_questions?slug='.$parlementaire->getSlug()); ?></h3>
      <?php include_partial('top', array('parlementaire'=>$parlementaire)); ?>
      <h3>Tags</h3>
<div style="text-align: justify">
<?php echo include_component('tag', 'tagcloud', array('tagquery' => $qtag, 'model' => 'Intervention', 'min_tag' => 2, 'route' => '@tag_parlementaire_interventions?parlementaire='.$parlementaire->slug.'&')); ?>
</div>
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
