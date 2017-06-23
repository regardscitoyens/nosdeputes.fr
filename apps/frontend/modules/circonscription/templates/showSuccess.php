<?php
$sf_response->setTitle($circo.' ('.$departement_num.') : Les députés par circonscription');
$listOptions = array('deputes' => $parlementaires, 'circo' => $departement_num);
if ($departement_num == "999")
  $listOptions["list"] = true;
?>
<h1>Les députés par circonscription</h1>
<div id="carte_circo">
  <h2><?php echo $circo.' ('.$departement_num.')'; ?><br/><small><?php echo $total; ?> députés</small></h2>
  <?php if ($departement_num != "999") include_partial('map', array('num'=>strtolower($departement_num), 'size' => 550)); ?>
  <div class="list_<?php echo ($departement_num == "999" ? 'table' : 'circo'); ?>">
    <?php include_partial('parlementaire/table', $listOptions); ?>
  </div>
  <?php if ($departement_num == "999") echo file_get_contents('circos-francais-etranger.svg'); ?>
</div>
