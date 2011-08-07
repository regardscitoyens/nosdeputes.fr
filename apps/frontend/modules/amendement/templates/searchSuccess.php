<?php $titre = 'Recherche d\'amendements portant sur "'.$mots.'"'; ?>
<h1><?php echo $titre; ?></h1>
<?php $sf_response->setTitle($titre);
echo include_component('amendement', 'pagerAmendements', array('amendement_query' => $query, 'highlight' => $high, 'mots' => $mots));
?>