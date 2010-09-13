<?php $titre = "Amendements";
$sf_response->setTitle('Les amendements de '.$parlementaire->nom.' - NosDéputés.fr');
echo include_component('parlementaire', 'header', array('parlementaire' => $parlementaire, 'titre' => $titre, 'rss' => '@parlementaire_amendements_rss?slug='.$parlementaire->slug));
echo include_component('amendement', 'pagerAmendements', array('amendement_query' => $amendements)); ?>
