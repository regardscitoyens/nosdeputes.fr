<h1>Les députés par circonscription</h1>
<div id="carte_circo">
<h2><?php echo $circo.' ('.$departement_num.')'; ?></h2>
<?php $sf_response->setTitle($circo.' ('.$departement_num.') : Les députés par circonscription'); ?>
<?php include_partial('map', array('num'=>strtolower($departement_num), 'size' => 400)); ?>
<p><?php echo $total; ?> députés trouvés :</p>
<div class="list_circo">
  <?php include_partial('parlementaire/table', array('deputes' => $parlementaires, 'circo' => $departement_num)); ?>
</div>
</div>
