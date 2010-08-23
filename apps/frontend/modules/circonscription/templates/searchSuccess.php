<h1>Les députés : recherche par circonscriptions</h1>
<?php $sf_response->setTitle('Les députés : recherche par circonscriptions'); ?>
<p><?php $cpt = $query_parlementaires->count(); echo $cpt ; ?> député<?php if ($cpt > 1) echo 's' ; ?> trouvé<?php if ($cpt > 1) echo 's' ; ?> pour "<i><?php echo $search; ?></i>":</p>
<div class="list_table">
  <?php include_partial('parlementaire/table', array('deputes' => $query_parlementaires->execute(), 'list' => 1, 'circo' => 1, 'dept' => 1)); ?>
</div>
