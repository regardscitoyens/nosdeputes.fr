  <div class="boite_depute" id="b1">
    <div class="b_d_h"><div class="b_d_hg"></div><div class="b_d_hd"></div></div>
    <div class="b_d_cont">
      <div class="b_d_infos">
      <h2> Informations</h2>
    <ul>
<?php if ($parlementaire->fin_mandat && $parlementaire->fin_mandat >= $parlementaire->debut_mandat) : ?>
      <li>Mandat clos rempli du <?php echo myTools::displayDate($parlementaire->debut_mandat); ?> au <?php echo myTools::displayDate($parlementaire->fin_mandat); ?> (<?php echo $parlementaire->getCauseFinMandat(); ?>)</li>
<?php else : ?>
      <li>Mandat en cours depuis le <?php echo myTools::displayDate($parlementaire->debut_mandat); ?>
      <?php foreach ($missions as $resp)
        if (preg_match('/^Mission temporaire/', $resp->getNom())) {
          echo '<br/>&nbsp;(en cours de mission pour le gouvernement)';
          break;
        } ?>
      </li>
<?php endif;
      if ($parlementaire->suppleant_de_id && $supplee = $parlementaire->getSuppleantDe())
        echo '<li>Suppléant'.($parlementaire->sexe == "F" ? 'e' : '').' de&nbsp;: '.link_to($supplee->nom, "@parlementaire?slug=".$supplee->slug).'</li>'; 
      if ($parlementaire->groupe_acronyme != "") : ?>
      <li>Groupe politique : <?php echo link_to(Organisme::getNomByAcro($parlementaire->groupe_acronyme), '@list_parlementaires_groupe?acro='.$parlementaire->groupe_acronyme); ?> (<?php echo $parlementaire->getgroupe()->getFonction(); ?>)</li>
      <?php endif; ?>
      <li>Profession : <?php if ($parlementaire->profession) : echo link_to($parlementaire->profession, '@list_parlementaires_profession?search='.$parlementaire->profession); else : ?>Non communiquée<?php endif; ?></li>
      <li><?php echo link_to('Fiche sur le site de l\'Assemblée nationale', $parlementaire->url_an, array('title' => 'Lien externe', 'rel'=>'nofollow')); ?></li>
      <li><a href="http://fr.wikipedia.org/wiki/<?php echo rawurlencode($parlementaire->nom); ?>">Page sur Wikipédia</a></li>
      <?php if ($parlementaire->sites_web) {
        $moreweb = "";
        foreach (unserialize($parlementaire->sites_web) as $site) if ($site) {
                $nomsite = "Site web";
                if (preg_match('/twitter/', $site)) $nomsite = "Sur Twitter";
                else if (preg_match('/facebook/', $site)) $nomsite = "Sur Facebook";
                $link = "<li>".link_to($nomsite, $site, array('title' => 'Lien externe', 'rel'=>'nofollow'))."</li>";
                if (preg_match('/twitter|facebook/', $site)) $moreweb .= $link;
                else echo $link;
        }
        echo $moreweb;
      } ?>
    </ul>
    <?php if ($parlementaire->fin_mandat == null || $parlementaire->fin_mandat < $parlementaire->debut_mandat) : ?>
      <h2>Responsabilités</h2>
      <ul>
        <li>Commission permanente : <ul><?php foreach ($commissions_permanentes as $resp) { echo '<li>'.link_to(ucfirst(str_replace('Commission ', '', preg_replace('/(Commission|et|,) d(u |e la |es |e l\'|e l’)/', '\\1 ', $resp->getNom()))), '@list_parlementaires_organisme?slug='.$resp->getSlug()); echo ' ('.$resp->getFonction().') </li>'; break; } ?></ul></li>
<?php if (count($missions)) : ?>
        <li>Missions parlementaires :
          <ul>
            <?php 
            foreach ($missions as $resp) { ?>
            <li><?php echo link_to($resp->getNom(), '@list_parlementaires_organisme?slug='.$resp->getSlug()); echo ' ('.$resp->getFonction().') '; ?></li>
            <?php } ?>
          </ul>
        </li>
<?php endif; ?>
        <?php if ($parlementaire->getExtras()) { ?>
        <li>Fonctions judiciaires, internationales ou extra-parlementaires&nbsp;:
          <ul>
            <?php foreach ($parlementaire->getExtras() as $extra) { ?>
            <li><?php echo link_to($extra->getNom(),'@list_parlementaires_organisme?slug='.$extra->getSlug() ); ?> (<?php echo $extra->getFonction(); ?>)</li>
            <?php } ?>
          </ul>
        </li>
        <?php } ?>
        <?php // if ($parlementaire->getGroupes()) {
//         echo "<li>Groupes d'études et d'amitié interparlementaires&nbsp;:<ul>";
//         foreach ($parlementaire->getGroupes() as $groupe)
//           echo "<li>".link_to($groupe->getNom(),'@list_parlementaires_organisme?slug='.$groupe->getSlug())." (".$groupe->getFonction().")</li>";
//         echo "</ul></li>";
//       } ?>
      </ul>
      <?php endif; ?> <!-- else : ajouter les infos venant de parsing ancien (anciennes responsabilités) et avant les respon actuelles de ministre machin via les personnalites get fonctions? -->
      </div>
    </div>
    <div class="b_d_b"><div class="b_d_bg"></div><div class="b_d_bd"></div></div>
    <div class="b_d_h"><div class="b_d_hg"></div><div class="b_d_hd"></div></div>
    <div class="b_d_cont">
      <div class="b_d_infos">
      <h2>Travaux législatifs</h2>
	<h3><?php if(myTools::isFinLegislature()) {
	  echo "Ses principaux dossiers de la législature";
	  $order = 'nb';
	}else{
	  echo "Ses derniers dossiers";
	  $order = 'date';
	}?></h3>
      <?php echo include_component('section', 'parlementaire', array('parlementaire' => $parlementaire, 'limit' => 4, 'order' => $order)); ?>
      <p class="suivant"><?php echo link_to('Tous ses dossiers', '@parlementaire_textes?slug='.$parlementaire->slug); ?></p>
      <h3><?php echo link_to('Travaux en commissions','@parlementaire_interventions?slug='.$parlementaire->getSlug().'&type=commission'); ?></h3>
      <h3><?php echo link_to('Travaux en hémicycle','@parlementaire_interventions?slug='.$parlementaire->getSlug().'&type=loi'); ?></h3>
      <h3><?php echo link_to('Toutes ses interventions','@parlementaire_interventions?slug='.$parlementaire->getSlug().'&type=all'); ?></h3>
      <h2>Questions au gouvernement</h2>
      <h3>Ses dernières questions orales</h3>
       <?php echo include_component('intervention', 'parlementaireQuestion', array('parlementaire' => $parlementaire, 'limit' => 4)); ?>
      <p class="suivant"><?php echo link_to('Toutes ses questions orales','@parlementaire_interventions?slug='.$parlementaire->getSlug().'&type=question'); ?></p>
      <h3>Ses dernières questions écrites</h3>
       <?php echo include_component('questions', 'parlementaire', array('parlementaire' => $parlementaire, 'limit' => 4)); ?>
      <p class="suivant"><?php echo link_to('Toutes ses questions écrites','@parlementaire_questions?slug='.$parlementaire->getSlug()); ?></p>
      </div>
    </div>
    <div class="b_d_b"><div class="b_d_bg"></div><div class="b_d_bd"></div></div>
  </div>
    
  <div class="boite_depute" id="b2">
    <div class="b_d_h"><div class="b_d_hg"></div><div class="b_d_hd"></div></div>
    <div class="b_d_cont">
      <div class="b_d_infos">
    <h2>Suivre l'activité du député</h2>
<table width=100% style="text-align: center"><tr>
       <td><a href="<?php echo url_for('@alerte_parlementaire?slug='.$parlementaire->slug); ?>"><?php echo image_tag('xneth/email.png', 'alt="Email"'); ?></a><br/><a href="<?php echo url_for('@alerte_parlementaire?slug='.$parlementaire->slug); ?>">par email</a></td>
       <td><a href="<?php echo url_for('@parlementaire_rss?slug='.$parlementaire->slug); ?>"><?php echo image_tag('xneth/rss_obliq.png', 'alt="Flux rss"'); ?></a><br/><a href="<?php echo url_for('@parlementaire_rss?slug='.$parlementaire->slug); ?>">par RSS</a></td>
</tr></table>
      <h2>Champ lexical <small>(<?php
if (myTools::isFinLegislature()) {
echo "sur l'ensemble de la législature";
}else{
echo "sur 12 mois";
}
?>)</small></h2>
      <div style="text-align: justify">
<?php echo include_component('tag', 'parlementaire', array('parlementaire'=>$parlementaire)); ?>
<p class="suivant"><?php echo link_to('Tous ses mots', '@parlementaire_tags?slug='.$parlementaire->slug); ?></p>
      </div>
      </div>
    </div>
    <div class="b_d_b"><div class="b_d_bg"></div><div class="b_d_bd"></div></div>
    <div class="b_d_h"><div class="b_d_hg"></div><div class="b_d_hd"></div></div>
    <div class="b_d_cont">
      <div class="b_d_infos">
      <h2>Productions parlementaires</h2>
      <h3>Ses derniers rapports</h3>
      <?php echo include_component('documents', 'parlementaire', array('parlementaire' => $parlementaire, 'limit' => 4, 'type' => 'rap')); ?>
      <p class="suivant"><?php echo link_to('Tous ses rapports', '@parlementaire_documents?slug='.$parlementaire->slug.'&type=rap'); ?></p>
      <h3>Ses dernières propositions de loi</h3>
      <?php echo include_component('documents', 'parlementaire', array('parlementaire' => $parlementaire, 'limit' => 4, 'type' => 'loi')); ?>
      <p class="suivant"><?php echo link_to('Toutes ses propositions de loi cosignées', '@parlementaire_documents?slug='.$parlementaire->slug.'&type=loi'); ?></p>
      <h3><?php echo link_to('Tous ses amendements','@parlementaire_amendements?slug='.$parlementaire->getSlug()); ?></h3>
      </div>
    </div>
    <div class="b_d_b"><div class="b_d_bg"></div><div class="b_d_bd"></div></div>
  </div>

  <div class="boite_depute" id="b4">
  </div>

