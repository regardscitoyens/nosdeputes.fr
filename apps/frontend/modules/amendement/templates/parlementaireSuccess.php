<?php
echo include_component('parlementaire', 'header', array('parlementaire' => $parlementaire, 'titre' => "Amendements", 'rss' => '@parlementaire_amendements_rss?slug='.$parlementaire->slug));
echo include_component('amendement', 'pagerAmendements', array('amendement_query' => $amendements)); ?>
