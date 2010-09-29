<h1 class="list_inter">Les députés par circonscription</h1>
<h2><?php echo $circo.' ('.$departement_num.')'; ?></h2>
<?php $sf_response->setTitle($circo.' ('.$departement_num.') : Les députés par circonscription'); ?>
<?php include_partial('map', array('num'=>strtolower($departement_num))); ?>
<p><?php echo $total; ?> députés trouvés :</p>
<div class="list_circo">
  <?php include_partial('parlementaire/table', array('deputes' => $parlementaires, 'circo' => $departement_num)); ?>
</div>
<script type="text/javascript">
/* survol du txt */
$(".list_dep").live("mouseover", function() {
  dep = $(this).attr("id").substring(3);
  $("#map"+dep).mouseover();
})
$(".list_dep").live("mouseout", function() {
  dep = $(this).attr("id").substring(3);
  $("#map"+dep).mouseout();
})
/* survol de la map */
$("area").live("mouseover", function() {
  dep = $(this).attr("id").substring(3);
  $("#dep"+dep).css("background-color", "#D1EA74");
})
$("area").live("mouseout", function() {
  dep = $(this).attr("id").substring(3);
  $("#dep"+dep).css("background-color", "#fff");
})
</script>
