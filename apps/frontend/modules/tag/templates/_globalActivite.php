<?php if (!count($itag)) return ; ?>
<div class="box_tags">
<h2><span style="margin-right: 5px;"><img alt="tags" src="<?php echo $sf_request->getRelativeUrlRoot(); ?>/images/xneth/assemblee-nationale.png" /></span><?php
if (myTools::isFinLegislature()) {
  $titretags = 'Les principaux mots clés de la législature';
}else{
  $titretags = 'En ce moment à l\'Assemblée nationale';
}
echo link_to($titretags, '@parlementaires_tags'); ?></h2>
<?php echo include_component('tag', 'tagcloud', array('tagquery' => $itag, 'model'=>'Intervention', 'min_tag'=>2, 'limit'=>90)); ?>
<p class="suivant" style="padding-right: 10px">
<?php
    echo link_to('Voir tous les mots clés', 'parlementaire/tag');

?>
</p>
</div>
