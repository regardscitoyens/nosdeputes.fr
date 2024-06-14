  <div class="boite_depute" id="b1">
    <h2>Informations</h2>
      <ul>
        <?php if ($main_fonction) echo "<li><b>$main_fonction de l'Assemblée nationale</b></li>"; ?>
        <?php if ($ministre) { ?>
        <li><b>Mandot clos <?php if (!$parlementaire->isEnMandat()) echo "(".myTools::displayDate($parlementaire->debut_mandat).' -> '.myTools::displayDate($parlementaire->fin_mandat).") "; ?>: membre du Gouvernement<br/><?php echo $ministre; ?></b>
        <?php } else if (!$parlementaire->isEnMandat()) { ?>
        <li><b>Mandat clos</b> rempli du <?php
echo myTools::displayDate($parlementaire->debut_mandat).' au '.myTools::displayDate($parlementaire->fin_mandat);
if ($cause = $parlementaire->getCauseFinMandat())
  echo " (".preg_replace("/^(.*sénat.*)$/i", link_to("\\1 &mdash; Voir sur NosSénateurs.fr", "https://www.nossenateurs.fr/".$parlementaire->slug), $parlementaire->getCauseFinMandat()).")";
        ?></li>
        <?php } else { ?>
        <li>Mandat en cours depuis le <?php echo myTools::displayDate($parlementaire->debut_mandat); foreach ($missions as $resp) if (preg_match('/^Mission temporaire/', $resp->getNom())) { echo '<br/>&nbsp;(en cours de mission pour le gouvernement)'; break; } ?>
        </li>
        <?php }
        $fem = ($parlementaire->sexe == "F" ? 'e' : '');
        if ($parlementaire->suppleant_de_id && $supplee = $parlementaire->getSuppleantDe())
          echo '<li>Suppléant'.$fem.' de&nbsp;: '.link_to($supplee->nom, "@parlementaire?slug=".$supplee->slug).'</li>';
        if (!$parlementaire->isEnMandat() && $suppleant = $parlementaire->getSuppleant()) {
          $supfem = ($suppleant->sexe == "F" ? 'e' : '');
          echo '<li><b>Suppléant'.$supfem.'&nbsp;: '.link_to($suppleant->nom, "@parlementaire?slug=".$suppleant->slug).'</li></b>';
        }
        if ($parlementaire->groupe_acronyme != "") : ?>
        <li>Groupe politique : <?php echo link_to(Organisme::getNomByAcro($parlementaire->groupe_acronyme), '@list_parlementaires_groupe?acro='.$parlementaire->groupe_acronyme); ?> <?php if ($parlementaire->groupe_acronyme !== "NI") : ?>(<?php echo preg_replace('/^(présidente?)$/i', '<strong>\1</strong>', ($parlementaire->getGroupe() ? $parlementaire->getGroupe()->getFonction() : 'ancien membre')); ?>)<?php endif; ?></li>
        <?php endif;
        if ($parlementaire->parti) : ?>
        <li>Parti politique (rattachement financier) : <?php echo $parlementaire->parti; ?></li>
        <?php endif;
        $tz  = new DateTimeZone('Europe/Paris');
        $age = DateTime::createFromFormat('Y-m-d', $parlementaire->date_naissance, $tz)
          ->diff(new DateTime('now', $tz))
          ->y;
        ?>
        <li>Né<?php if ($parlementaire->sexe == "F") echo 'e'; ?> le : <?php echo myTools::displayDate($parlementaire->date_naissance)." ($age ans)"; ?> à <?php echo $parlementaire->lieu_naissance; ?></li>
        <li>Profession : <?php if ($parlementaire->profession) : echo link_to($parlementaire->profession, myTools::get_solr_list_url($parlementaire->profession, '', 'Parlementaire', "profession=".myTools::solrize($parlementaire->profession))."&noredirect=1"); else : ?>Non communiquée<?php endif; ?></li>
        <li>Liens :
          <ul><?php
 if ($parlementaire->url_an) echo '<li>'.link_to('Fiche Assemblée nationale', $parlementaire->url_an, array('title' => 'Lien externe', 'rel'=>'nofollow')).'</li>';
  echo '<li><a href="https://fr.wikipedia.org/wiki/'.rawurlencode($parlementaire->nom).'">Page Wikipédia</a></li>';
if ($parlementaire->sites_web) {
  $moreweb = "";
  foreach ($parlementaire->getSitesWeb() as $site) if ($site && !preg_match('/assemblee-nationale\.fr\/deputes\/fiche/', $site) && preg_match('/^http/', $site)) {
    $nomsite = "Site web : ".$site;
    if (preg_match('/twitter/', $site)) $nomsite = "Compte Twitter : ".preg_replace("/^.*[^a-z0-9_]([a-z0-9_]+)$/i", "@\\1", $site);
    else if (preg_match('/facebook/', $site)) $nomsite = "Page Facebook";
    $link = "<li>".link_to($nomsite, $site, array('title' => 'Lien externe', 'rel'=>'nofollow'))."</li>";
    if (!preg_match('/twitter|facebook/', $site)) $moreweb .= $link;
    else echo $link;
  }
  echo $moreweb;
}
        ?></ul>
      </ul>

  <?php if (!$ministre) :
    $mails = $parlementaire->getMails();
    $adresses = $parlementaire->getAdresses();
    $collabs = $parlementaire->getCollaborateurs(); ?>
    <h2>Contact</h2>
      <ul>
        <li>Par e-mail :
          <?php if ($mails && count($mails) && $mails[0]) : ?>
          <ul>
            <?php foreach ($mails as $mail) : ?>
            <li><a href="mailto:<?php echo $mail; ?>"><?php echo $mail; ?></a></li>
            <?php endforeach; ?>
          </ul>
          <?php else : ?> non renseigné
          <?php endif; ?>
        </li>
        <li>Par courrier :
          <?php if ($adresses && count($adresses)) : ?>
          <ul>
            <?php foreach ($adresses as $addr) : ?>
            <li><?php echo preg_replace('/ Télé(phone|copie) :/i', '', $addr); ?></li>
            <?php endforeach; ?>
          </ul>
          <?php else : ?> non renseigné
          <?php endif; ?>
        </li>
        <?php if ($collabs && count($collabs)) : ?>
        <li>Collaborateurs :
          <ul>
            <?php foreach ($collabs as $collab) : ?>
            <li><?php echo $collab; ?></li>
            <?php endforeach; ?>
          </ul>
        </li>
        <?php endif; ?>
      </ul>
    <?php endif; // if $ministre ?>

    <?php if ($parlementaire->isEnMandat()) : ?>
    <h2>Responsabilités</h2>
      <ul>
        <?php if (!myTools::isEmptyLegislature()) : ?>
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
        <?php endif ?>
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
        <?php endif;
        $extras = $parlementaire->getExtras();
        if (count($extras) == 1 && $extras[0]->nom == "Gouvernement")
         $extras = array();
        if ($extras) : ?>
        <li>Fonctions judiciaires, internationales ou extra-parlementaires&nbsp;:
          <ul><?php foreach ($extras as $extra) { ?>
            <li><?php echo link_to($extra->getNom(),'@list_parlementaires_organisme?slug='.$extra->getSlug() ); ?> (<?php echo $extra->getFonction(); ?>)</li>
          <?php } ?></ul>
        </li>
        <?php endif ?>
        <?php if ($parlementaire->getGroupes()) : ?>
        <li>Groupes d'études et d'amitié&nbsp;:
          <ul><?php foreach ($parlementaire->getGroupes() as $gpe) { ?>
            <li><?php echo link_to($gpe->getNom(),'@list_parlementaires_organisme?slug='.$gpe->getSlug() ); ?> (<?php echo $gpe->getFonction(); ?>)</li>
          <?php } ?></ul>
        </li>
        <?php endif ?>
      </ul>
    <?php endif ?>

    <?php if (!myTools::isEmptyLegislature() && !$ministre) : ?>
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
      <h3>Ses interventions</h3>
      <p class="paddingleft"><?php echo link_to('Consulter ses travaux en commissions', myTools::get_solr_list_url('', $parlementaire->nom, 'Intervention', 'type=commission')); ?></p>
      <p class="paddingleft"><?php echo link_to('Consulter ses travaux en hémicycle', myTools::get_solr_list_url('', $parlementaire->nom, 'Intervention', 'type=loi')); ?></p>
      <p class="paddingleft"><?php echo link_to('Consulter toutes ses interventions', myTools::get_solr_list_url('', $parlementaire->nom, 'Intervention')); ?></p>

      <div class="titre_amendements">
        <h3>Ses amendements</h3>
        <p class="paddingleft"><?php echo link_to('Consulter tous ses amendements', myTools::get_solr_list_url('', $parlementaire->nom, 'Amendement')); ?></p>
      </div>
      <?php echo include_component('amendement', 'parlementaireStats', array('parlementaire' => $parlementaire)); ?>
    <?php endif ?>
  </div>

  <div class="boite_depute" id="b2">
    <?php if (!$parlementaire->isEnMandat() || $ministre) : ?>
    <h2>Le mandat de <?php echo $parlementaire->getCeCette(false); ?> est achevé</h2>
    <?php else : ?>
    <h2>Suivre l'activité d<?php echo ($parlementaire->sexe == "F" ? "e la députée" : "u député"); ?></h2>
      <table width=100% style="text-align: center"><tr>
        <td width=33%><a href="<?php echo url_for('@alerte_parlementaire?slug='.$parlementaire->slug); ?>"><?php echo image_tag('xneth/email.png', 'alt="e-mail"'); ?></a><br/><a href="<?php echo url_for('@alerte_parlementaire?slug='.$parlementaire->slug); ?>">par e-mail</a></td>
        <td width=33%><a href="<?php echo url_for('@parlementaire_rss?slug='.$parlementaire->slug); ?>"><?php echo image_tag('xneth/rss_obliq.png', 'alt="Flux rss"'); ?></a><br/><a href="<?php echo url_for('@parlementaire_rss?slug='.$parlementaire->slug); ?>">par RSS</a></td>
        <td width=33%><a href="<?php echo url_for('@widget?depute='.$parlementaire->slug); ?>"><?php echo image_tag('xneth/widget.png', 'alt="Flux rss"'); ?></a><br/><a href="<?php echo url_for('@widget?depute='.$parlementaire->slug); ?>">sur mon site</a></td>
      </tr></table>
    <?php endif; ?>

    <?php if (!myTools::isEmptyLegislature() && !$ministre) : ?>
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

    <h2>Votes</h2>
      <h3>Ses derniers votes</h3>
       <?php echo include_component('scrutin', 'parlementaire', array('parlementaire' => $parlementaire, 'limit' => 4)); ?>
      <p class="suivant"><?php echo link_to('Tous ses votes', '@parlementaire_votes?slug='.$parlementaire->slug); ?></p>

    <h2>Questions au gouvernement</h2>
      <h3>Ses dernières questions orales</h3>
       <?php echo include_component('intervention', 'parlementaireQuestion', array('parlementaire' => $parlementaire, 'limit' => 4)); ?>
      <h3>Ses dernières questions écrites</h3>
       <?php echo include_component('questions', 'parlementaire', array('parlementaire' => $parlementaire, 'limit' => 4)); ?>
    <?php endif ?>

    <?php if ($historique || $anciens_mandats) : ?>
    <h2>Historique des fonctions et mandats</h2>
      <ul><?php if ($parlementaire->url_ancien_cpc)
        echo '<li><a href="'.$parlementaire->url_ancien_cpc.'"><strong>Député'.$fem.' réélu'.$fem.' : voir sa page NosDéputés.fr de la précédente législature</strong></a><br/><br/></li>';
      foreach ($historique as $resp) : ?>
        <li><?php
if ($resp->type == "groupe") {
  $acro = $resp->Organisme->getSmallNomGroupe();
  echo link_to(($acro != "NI" ? "Groupe " : "").$resp->getNom(), '@list_parlementaires_groupe?acro='.$acro);
} else echo link_to($resp->getNom(), '@list_parlementaires_organisme?slug='.$resp->getSlug());
$fonction = preg_replace('/^(.*(président|rapporteur|questeur)[^,]*)/i', '<strong>\1</strong>', $resp->getFonction());
echo " ($fonction du ";
echo myTools::displayDate($resp->debut_fonction).' au '.myTools::displayDate($resp->fin_fonction).')';
        ?></li>
      <?php endforeach;
      foreach ($anciens_mandats as $m) : ?>
        <li><?php echo $m; ?></li>
      <?php endforeach ?></ul>
    <?php endif ?>

  </div>
