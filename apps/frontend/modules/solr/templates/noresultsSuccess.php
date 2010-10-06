<div>
<?php include_partial('solr/searchbox'); ?>
<p>Désolé, nous n'avons pas trouvé de résultat pour votre recherche.</p>
<?php include_component('parlementaire', 'search', array('query' => $query, 'msg'=>"Peut être recherchiez vous l'un de ces députés :")); ?>
<p><a href="<?php echo url_for('alerte/create?query='.urlencode($query)); ?>"><?php echo image_tag('xneth/email.png', 'alt="Email :"'); ?>
Etre alerté par email lorsque cette recherche comportera un résultat</a></p>
</div>
