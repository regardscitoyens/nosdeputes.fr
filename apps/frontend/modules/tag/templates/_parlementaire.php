<div>
<?php echo include_component('tag', 'tagcloud', array('tagquery' => $qtag, 'model' => 'Intervention', 'min_tag' => 2, 'route' => '@tag_parlementaire_interventions?parlementaire='.$parlementaire->slug.'&', 'limit' => 100)); ?>
</div>
