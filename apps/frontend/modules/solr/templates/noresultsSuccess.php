<?php $sf_response->setTitle("NosDéputés.fr - Aucun résultat pour votre recherche"); ?>
<div class="solr">
<?php include_partial('solr/follow', array('query' => $query, 'selected' => array(), 'norss' => true, 'zero' => true)); ?>
<?php include_partial('solr/searchbox'); ?>
<p>Désolé, nous n'avons pas trouvé de résultat pour votre recherche.</p>
<?php include_component('parlementaire', 'search', array('query' => $query, 'msg'=>"Peut être recherchiez vous l'un de ces députés :")); ?>
</div>
