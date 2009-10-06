<h1>Les députés par circonscriptions</h1>
<h1><?php echo $circo; ?></h1>
<?php $sf_response->setTitle($circo.' ('.$departement_num.') : Les députés par circonscriptions'); ?>
<?php include_partial('map', array('num'=>$departement_num)); ?>
<p><?php echo $circo; ?> : <?php echo $query_parlementaires->count(); ?> députés trouvés :</p>
<ul>
<?php foreach($query_parlementaires->execute() as $parlementaire) : ?>
<li><?php
echo $parlementaire->getNumCircoString(1); ?>
&nbsp;:
<?php
echo link_to($parlementaire->nom, 'parlementaire/show?slug='.$parlementaire->slug); 
?>
&nbsp;(<?php echo $parlementaire->getStatut(1); ?>)</li>
<?php endforeach ; ?>
</ul>
