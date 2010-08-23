<h1>Les députés par circonscriptions</h1>
<h2><?php echo $circo.' ('.$departement_num.')'; ?></h2>
<?php $sf_response->setTitle($circo.' ('.$departement_num.') : Les députés par circonscriptions'); ?>
<?php include_partial('map', array('num'=>strtolower($departement_num))); ?>
<p><?php echo $total; ?> députés trouvés :</p>
<div class="list_circo">
  <?php include_partial('parlementaire/table', array('deputes' => $parlementaires, 'circo' => $departement_num)); ?>
</div>
