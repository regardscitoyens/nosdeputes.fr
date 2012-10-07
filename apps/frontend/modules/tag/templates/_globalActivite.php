<?php echo include_component('tag', 'tagcloud', array('tagquery' => $itag, 'model'=>'Intervention', 'min_tag'=>2, 'limit'=>90)); ?>
<p class="suivant" style="padding-right: 10px">
<?php 
    echo link_to('Voir tous les mots clés', 'parlementaire/tag'); 

?>
</p>
