<h1>Les sénateurs par circonscription</h1>
<div id="carte_circo">
<h2><?php echo $circo.' ('.$departement_num.')'; ?></h2>
<?php $sf_response->setTitle($circo.' ('.$departement_num.') : Les sénateurs par circonscription'); ?>
<?php include_partial('map', array('num'=>strtolower($departement_num), 'size' => 400)); ?>
<p><?php echo $total; ?> sénateurs trouvés :</p>
<div class="list_circo">
  <?php include_partial('parlementaire/table', array('senateurs' => $parlementaires, 'circo' => $departement_num)); ?>
</div>
</div>
