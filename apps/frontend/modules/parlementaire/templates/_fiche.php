  <div class="boite_depute" id="b1">
    <h2>Informations</h2>
      <ul>
        <?php if ($parlementaire->fin_mandat && $parlementaire->fin_mandat >= $parlementaire->debut_mandat) : ?>
        <li>Mandat clos rempli du <?php
echo myTools::displayDate($parlementaire->debut_mandat).' au '.myTools::displayDate($parlementaire->fin_mandat);
if ($cause = $parlementaire->getCauseFinMandat())
  echo " (".preg_replace("/^(.*sénat.*)$/i", link_to("\\1 &mdash; Voir sur NosSénateurs.fr", "http://www.nossenateurs.fr/".$parlementaire->slug), $parlementaire->getCauseFinMandat()).")";
        ?></li>
        <?php else : ?>
        <li>Mandat en cours depuis le <?php echo myTools::displayDate($parlementaire->debut_mandat); foreach ($missions as $resp) if (preg_match('/^Mission temporaire/', $resp->getNom())) { echo '<br/>&nbsp;(en cours de mission pour le gouvernement)'; break; } ?>
        </li>
        <?php endif;
        $fem = ($parlementaire->sexe == "F" ? 'e' : '');
        if ($parlementaire->url_ancien_cpc)
          echo '<li><a href="'.$parlementaire->url_ancien_cpc.'"><strong>Député'.$fem.' réélu'.$fem.' : voir sa page NosDéputés.fr de la précédente législature</strong></a></li>';
        if ($parlementaire->suppleant_de_id && $supplee = $parlementaire->getSuppleantDe())
          echo '<li>Suppléant'.$fem.' de&nbsp;: '.link_to($supplee->nom, "@parlementaire?slug=".$supplee->slug).'</li>';
        if ($parlementaire->groupe_acronyme != "") : ?>
        <li>Groupe politique : <?php echo link_to(Organisme::getNomByAcro($parlementaire->groupe_acronyme), '@list_parlementaires_groupe?acro='.$parlementaire->groupe_acronyme); ?> (<?php echo $parlementaire->getGroupe()->getFonction(); ?>)</li>
        <?php endif;
        if ($parlementaire->parti) : ?>
        <li>Parti politique (rattachement financier) : <?php echo $parlementaire->parti; ?></li>
        <?php endif; ?>
        <li>Profession : <?php if ($parlementaire->profession) : echo link_to($parlementaire->profession, myTools::get_solr_list_url($parlementaire->profession, '', 'Parlementaire', "profession=".myTools::solrize($parlementaire->profession))."&noredirect=1"); else : ?>Non communiquée<?php endif; ?></li>
        <?php
 if ($parlementaire->url_an) echo '<li>'.link_to('Fiche Assemblée nationale', $parlementaire->url_an, array('title' => 'Lien externe', 'rel'=>'nofollow')).'</li>';
  echo '<li><a href="http://fr.wikipedia.org/wiki/'.rawurlencode($parlementaire->nom).'">Page Wikipédia</a></li>';
if ($parlementaire->sites_web) {
  $moreweb = "";
  foreach (unserialize($parlementaire->sites_web) as $site) if ($site && !preg_match('/assemblee-nationale\.fr\/deputes\/fiche/', $site)) {
    $nomsite = "Site web : ".$site;
    if (preg_match('/twitter/', $site)) $nomsite = "Twitter : ".preg_replace("/^.*[^a-z0-9_]([a-z0-9_]+)$/i", "@\\1", $site);
    else if (preg_match('/facebook/', $site)) $nomsite = "Page Facebook";
    $link = "<li>".link_to($nomsite, $site, array('title' => 'Lien externe', 'rel'=>'nofollow'))."</li>";
    if (!preg_match('/twitter|facebook/', $site)) $moreweb .= $link;
    else echo $link;
  }
  echo $moreweb;
}
        ?>
      </ul>

    <h2>Contact</h2>
      <ul>
        <li>Par e-mail :
          <ul>
            <?php foreach (unserialize($parlementaire->mails) as $mail) : ?>
            <li><?php echo $mail ?></li>
            <?php endforeach; ?>
          </ul>
        </li>
        <li>Par courrier :
          <ul>
            <?php foreach (unserialize($parlementaire->adresses) as $addr) : ?>
            <li><?php echo preg_replace('/ Télé(phone|copie) :/i', '', $addr) ?></li>
            <?php endforeach; ?>
          </ul>
        </li>
        <?php if ($parlementaire->collaborateurs) : ?>
        <li>Collaborateurs :
          <ul>
            <?php foreach (unserialize($parlementaire->collaborateurs) as $collab) : ?>
            <li><?php echo $collab ?></li>
            <?php endforeach; ?>
          </ul>
        </li>
        <?php endif; ?>
      </ul>

    <?php if ($parlementaire->fin_mandat == null || $parlementaire->fin_mandat < $parlementaire->debut_mandat) : ?>
    <h2>Responsabilités</h2>
      <ul>
        <li>Commission permanente :
          <ul>
            <li><?php if ($commission_permanente) {
$name = ucfirst(str_replace('Commission ', '', preg_replace('/(Commission|et|,) d(u |e la |es |e l\'|e l’)/',
'\\1 ', $commission_permanente->getNom())));
echo link_to($name, '@list_parlementaires_organisme?slug='.$commission_permanente->getSlug());
$fonction = preg_replace('/^(.*(président|rapporteur|questeur)[^,]*)/i', '<strong>\1</strong>', $commission_permanente->getFonction());
echo " ($fonction)";
            } ?></li>
          </ul>
        </li>
        <?php if (count($missions)) : ?>
        <li>Missions parlementaires :
          <ul><?php foreach ($missions as $resp) : ?>
            <li><?php
echo link_to($resp->getNom(), '@list_parlementaires_organisme?slug='.$resp->getSlug());
$fonction = preg_replace('/^(.*(président|rapporteur|questeur)[^,]*)/i', '<strong>\1</strong>', $resp->getFonction());
echo " ($fonction)";
            ?></li>
          <?php endforeach ?></ul>
        </li>
        <?php endif; ?>
        <?php if ($parlementaire->getExtras()) : ?>
        <li>Fonctions judiciaires, internationales ou extra-parlementaires&nbsp;:
          <ul>
            <?php foreach ($parlementaire->getExtras() as $extra) { ?>
            <li><?php echo link_to($extra->getNom(),'@list_parlementaires_organisme?slug='.$extra->getSlug() ); ?> (<?php echo $extra->getFonction(); ?>)</li>
              <?php } ?>
          </ul>
        </li>
        <?php endif ?>
      </ul>
    <?php endif ?>

    <h2>Travaux législatifs</h2>
      <h3><?php
if(myTools::isFinLegislature()) {
  echo "Ses principaux dossiers durant la législature";
  $order = 'nb';
} else {
  echo "Ses derniers dossiers";
  $order = 'date';
}
      ?></h3>
      <?php echo include_component('section', 'parlementaire', array('parlementaire' => $parlementaire, 'limit' => 4, 'order' => $order)); ?>
      <h3><?php echo link_to('Travaux en commissions', myTools::get_solr_list_url('', $parlementaire->nom, 'Intervention', 'type=commission')); ?></h3>
      <h3><?php echo link_to('Travaux en hémicycle', myTools::get_solr_list_url('', $parlementaire->nom, 'Intervention', 'type=loi')); ?></h3>
      <h3><?php echo link_to('Toutes ses interventions', myTools::get_solr_list_url('', $parlementaire->nom, 'Intervention')); ?></h3>
      <h3><?php echo link_to('Tous ses amendements', myTools::get_solr_list_url('', $parlementaire->nom, 'Amendement')); ?></h3>

    <h2>Questions au gouvernement</h2>
      <h3>Ses dernières questions orales</h3>
       <?php echo include_component('intervention', 'parlementaireQuestion', array('parlementaire' => $parlementaire, 'limit' => 4)); ?>
      <h3>Ses dernières questions écrites</h3>
       <?php echo include_component('questions', 'parlementaire', array('parlementaire' => $parlementaire, 'limit' => 4)); ?>
  </div>

  <div class="boite_depute" id="b2">
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

    <h2>Productions parlementaires</h2>
      <h3>Ses derniers rapports</h3>
      <?php echo include_component('documents', 'parlementaire', array('parlementaire' => $parlementaire, 'limit' => 4, 'type' => 'rap')); ?>
      <h3>Ses dernières propositions de loi</h3>
      <?php echo include_component('documents', 'parlementaire', array('parlementaire' => $parlementaire, 'limit' => 4, 'type' => 'loi')); ?>
      <p class="suivant"><?php echo link_to('Toutes ses propositions de loi cosignées', '@parlementaire_documents?slug='.$parlementaire->slug.'&type=loi'); ?></p>

    <?php if ($historique) : ?>
    <h2>Historique de mandat</h2>
      <ul><?php foreach ($historique as $resp) : ?>
        <li><?php
if ($resp->type == "groupe") {
  $acro = $resp->Organisme->getSmallNomGroupe();
  echo link_to(($acro != "NI" ? "Groupe " : "").$resp->getNom(), '@list_parlementaires_groupe?acro='.$acro);
} else echo link_to($resp->getNom(), '@list_parlementaires_organisme?slug='.$resp->getSlug());
$fonction = preg_replace('/^(.*(président|rapporteur|questeur)[^,]*)/i', '<strong>\1</strong>', $resp->getFonction());
echo " ($fonction du ";
echo myTools::displayDate($resp->debut_fonction).' au '.myTools::displayDate($resp->fin_fonction).')';
        ?></li>
      <?php endforeach ?></ul>
    <?php endif ?>

  </div>
