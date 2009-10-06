<h1>Les députés par circonscriptions</h1>
<h2><?php echo $circo.' ('.$departement_num.')'; ?></h2>
<?php $sf_response->setTitle($circo.' ('.$departement_num.') : Les députés par circonscriptions'); ?>
<?php include_partial('map', array('num'=>strtolower($departement_num))); ?>
<p><?php echo $total; ?> députés trouvés :</p>
<ul>
<?php foreach($parlementaires as $parlementaire) : ?>
<li><?php
echo $parlementaire->getNumCircoString(1); ?>
&nbsp;:
<?php
echo link_to($parlementaire->nom, 'parlementaire/show?slug='.$parlementaire->slug); 
?>
&nbsp;(<?php echo $parlementaire->getStatut(1); ?>)</li>
<?php endforeach ; ?>
</ul>
