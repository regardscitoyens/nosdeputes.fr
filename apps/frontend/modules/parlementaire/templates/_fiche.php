<div class="row">
  <div class="boite_depute small-12 columns">
    <h3 class="deputes secondary">Informations</h3>
    <ul class="no-bullet">
      <?php if ($parlementaire->fin_mandat && $parlementaire->fin_mandat >= $parlementaire->debut_mandat) : ?>
      <li>Mandat clos rempli du <?php echo myTools::displayDate($parlementaire->debut_mandat); ?> au <?php echo myTools::displayDate($parlementaire->fin_mandat);
if ($cause = $parlementaire->getCauseFinMandat()) {
  echo " (".preg_replace("/^(.*sénat.*)$/i", link_to("\\1 &mdash; Voir sur NosSénateurs.fr", "http://www.nossenateurs.fr/".$parlementaire->slug), $parlementaire->getCauseFinMandat()).")";
} ?>
</li>
<?php else : ?>
      <li>Mandat en cours depuis le <?php echo myTools::displayDate($parlementaire->debut_mandat); ?>
        <?php foreach ($missions as $resp)
          if (preg_match('/^Mission temporaire/', $resp->getNom())) {
            echo '<span>(en cours de mission pour le gouvernement)</span>';
            break;
          } ?>
      </li>
      <?php endif;
        if ($parlementaire->url_ancien_cpc)
          echo '<li><a href="'.$parlementaire->url_ancien_cpc.'"><strong>Sa page NosDéputés.fr pour l\'ancienne législature</strong></a></li>';
        if ($parlementaire->suppleant_de_id && $supplee = $parlementaire->getSuppleantDe())
          echo '<li>Suppléant'.($parlementaire->sexe == "F" ? 'e' : '').' de&nbsp;: '.link_to($supplee->nom, "@parlementaire?slug=".$supplee->slug).'</li>';
        if ($parlementaire->groupe_acronyme != "") : ?>
          <li>Groupe politique : <?php echo link_to(Organisme::getNomByAcro($parlementaire->groupe_acronyme), '@list_parlementaires_groupe?acro='.$parlementaire->groupe_acronyme); ?> (<?php echo $parlementaire->getGroupe()->getFonction(); ?>)</li>
        <?php endif;
        if ($parlementaire->parti) : ?>
          <li>Parti politique (rattachement financier) : <?php echo $parlementaire->parti; ?></li>
        <?php endif; ?>
        <li>Profession : <?php if ($parlementaire->profession) : echo link_to($parlementaire->profession, myTools::get_solr_list_url($parlementaire->profession, '', 'Parlementaire', "profession=".myTools::solrize($parlementaire->profession))."&noredirect=1"); else : ?>Non communiquée<?php endif; ?></li>
        <?php if ($parlementaire->url_an) echo '<li>'.link_to('Page sur le site de l\'Assemblée nationale', $parlementaire->url_an, array('title' => 'Lien externe', 'rel'=>'nofollow')).'</li>'; ?>
        <li><a href="http://fr.wikipedia.org/wiki/<?php echo rawurlencode($parlementaire->nom); ?>">Page sur Wikipédia</a></li>
        <?php if ($parlementaire->sites_web) {
          $moreweb = "";
          foreach (unserialize($parlementaire->sites_web) as $site) if ($site && !preg_match('/assemblee-nationale\.fr\/deputes\/fiche/', $site)) {
                  $nomsite = "Site web";
                  if (preg_match('/twitter/', $site)) $nomsite = "Sur Twitter";
                  else if (preg_match('/facebook/', $site)) $nomsite = "Sur Facebook";
                  $link = "<li>".link_to($nomsite, $site, array('title' => 'Lien externe', 'rel'=>'nofollow'))."</li>";
                  if (preg_match('/twitter|facebook/', $site)) $moreweb .= $link;
                  else echo $link;
          }
          echo $moreweb;
        }
        ?>
    </ul>
    <?php
    $note_fonction = false;
    if ($parlementaire->fin_mandat == null || $parlementaire->fin_mandat < $parlementaire->debut_mandat) : ?>
      <h3 class="deputes secondary">Responsabilités</h3>
      <ul class="no-bullet">
        <li>Commission permanente:
          <ul><?php foreach ($commissions_permanentes as $resp) { echo '<li>'.link_to(ucfirst(str_replace('Commission ', '', preg_replace('/(Commission|et|,) d(u |e la |es |e l\'|e l’)/', '\\1 ', $resp->getNom()))), '@list_parlementaires_organisme?slug='.$resp->getSlug());
            $fonction = preg_replace('/^(.*(président|rapporteur|questeur)[^,]*)/i', '<strong>\1</strong>', $resp->getFonction());
            echo " ($fonction)";
            echo '</li>';
            break; } ?>
          </ul>
        </li>
        <?php if (count($missions)) : ?>
        <li>Missions parlementaires :
          <ul>
            <?php foreach ($missions as $resp) { ?>
              <li><?php echo link_to($resp->getNom(), '@list_parlementaires_organisme?slug='.$resp->getSlug());
              $fonction = preg_replace('/^(.*(président|rapporteur|questeur)[^,]*)/i', '<strong>\1</strong>', $resp->getFonction());
              echo " ($fonction)";
    ?></li>
            <?php } ?>
          </ul>
        </li>
        <?php endif; ?>
        <?php if ($parlementaire->getExtras()) { ?>
        <li>Fonctions judiciaires, internationales ou extra-parlementaires:
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
      <?php endif; // else : ajouter les infos venant de parsing ancien (anciennes responsabilités) et avant les respon actuelles de ministre machin via les personnalites get fonctions? ?>
 
      <h3 class="deputes secondary">Travaux législatifs</h3>
      <h4><?php if(myTools::isFinLegislature()) {
    echo "Ses principaux dossiers durant la législature";
    $order = 'nb';
  }else{
    echo "Ses derniers dossiers";
    $order = 'date';
  }?></h4>
      <?php echo include_component('section', 'parlementaire', array('parlementaire' => $parlementaire, 'limit' => 4, 'order' => $order)); ?>
      <h3><?php echo link_to('Travaux en commissions', myTools::get_solr_list_url('', $parlementaire->nom, 'Intervention', 'type=commission')); ?></h3>
      <h3><?php echo link_to('Travaux en hémicycle', myTools::get_solr_list_url('', $parlementaire->nom, 'Intervention', 'type=loi')); ?></h3>
      <h3><?php echo link_to('Toutes ses interventions', myTools::get_solr_list_url('', $parlementaire->nom, 'Intervention')); ?></h3>
      <h2>Questions au gouvernement</h2>
      <h3>Ses dernières questions orales</h3>
       <?php echo include_component('intervention', 'parlementaireQuestion', array('parlementaire' => $parlementaire, 'limit' => 4)); ?>
      <h3>Ses dernières questions écrites</h3>
       <?php echo include_component('questions', 'parlementaire', array('parlementaire' => $parlementaire, 'limit' => 4)); ?>
</div>

  <div class="boite_depute" id="b2">
    <div class="b_d_h"><div class="b_d_hg"></div><div class="b_d_hd"></div></div>
    <div class="b_d_cont">
      <div class="b_d_infos">
       <?php if ($parlementaire->fin_mandat && $parlementaire->fin_mandat >= $parlementaire->debut_mandat) { ?>
    <h2>Le mandat de ce député est achevé.</h2>
       <?php } else { ?>
    <h2>Suivre l'activité du député</h2>
<table width=100% style="text-align: center"><tr>
       <td width=33%><a href="<?php echo url_for('@alerte_parlementaire?slug='.$parlementaire->slug); ?>"><?php echo image_tag('xneth/email.png', 'alt="Email"'); ?></a><br/><a href="<?php echo url_for('@alerte_parlementaire?slug='.$parlementaire->slug); ?>">par email</a></td>
       <td width=33%><a href="<?php echo url_for('@parlementaire_rss?slug='.$parlementaire->slug); ?>"><?php echo image_tag('xneth/rss_obliq.png', 'alt="Flux rss"'); ?></a><br/><a href="<?php echo url_for('@parlementaire_rss?slug='.$parlementaire->slug); ?>">par RSS</a></td>
       <td width=33%><a href="<?php echo url_for('@widget?depute='.$parlementaire->slug); ?>"><?php echo image_tag('xneth/widget.png', 'alt="Flux rss"'); ?></a><br/><a href="<?php echo url_for('@widget?depute='.$parlementaire->slug); ?>">sur mon site</a></td>
</tr></table>
       <?php } ?>
      <h2>Champ lexical <small>(<?php
if (myTools::isFinLegislature()) {
echo "sur l'ensemble de la législature";
}else{
$mois = min(12, floor((time() - strtotime($parlementaire->debut_mandat) ) / (60*60*24*30)));
echo "sur $mois mois";
}
?>)</small></h2>
      <div style="text-align: justify">
<?php echo include_component('tag', 'parlementaire', array('parlementaire'=>$parlementaire)); ?>
<p class="suivant"><?php $end = ""; if (myTools::isFinLegislature()) $end = '&all=1'; echo link_to('Tous ses mots', '@parlementaire_tags?slug='.$parlementaire->slug.$end); ?></p>
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
      <h3>Ses dernières propositions de loi</h3>
      <?php echo include_component('documents', 'parlementaire', array('parlementaire' => $parlementaire, 'limit' => 4, 'type' => 'loi')); ?>
      <p class="suivant"><?php echo link_to('Toutes ses propositions de loi cosignées', '@parlementaire_documents?slug='.$parlementaire->slug.'&type=loi'); ?></p>
      <h3><?php echo link_to('Tous ses amendements', myTools::get_solr_list_url('', $parlementaire->nom, 'Amendement')); ?></h3>
      </div>
    </div>
    <div class="b_d_b"><div class="b_d_bg"></div><div class="b_d_bd"></div></div>
  </div>

  <div class="boite_depute" id="b4">
  </div>
<script type="text/javascript">
$.each($('.email'), function() {
  $(this).attr('href', $(this).attr('href').replace(RegExp('(an@parl)','g'),'@'))
});
</script>
