<?php
$titre = ucfirst($typetitre);
$sf_response->setTitle('Les '.$typetitre.' de '.$parlementaire->nom.' - NosDéputés.fr');
echo include_component('parlementaire', 'header', array('parlementaire' => $parlementaire, 'titre' => $titre, 'rss' => '@parlementaire_documents_rss?slug='.$parlementaire->slug.'&type='.$type));
// echo include_component('parlementaire', 'header', array('parlementaire' => $parlementaire, 'titre' => $titre, 'rss' => '@parlementaire_documents_rss?slug='.$parlementaire->slug.'&type='.$type));
?>
<div class="documents">
<?php echo include_component('documents', 'pagerDocuments', array('document_query' => $docs, 'typetitre' => $typetitre, 'feminin' => $feminin)); ?>
</div>
