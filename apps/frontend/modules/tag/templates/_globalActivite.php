
<?php echo include_component('tag', 'tagcloud', array('tagquery' => $itag, 'model'=>'Intervention', 'min_tag'=>2, 'limit'=>90)); ?>

<p class="text-center">
  <?php echo link_to('Voir tous les mots clés', 'parlementaire/tag', array('class' => 'button')); ?>
</p>


