<div id="carte_circo">
<h1>Toutes les circonscriptions par département</h1>
<?php include_partial('circonscription/mapDepartement', array('width'=>500, 'height'=>0)); ?>
<div class="list_deptmts dept_col0">
<?php $iters = array("0" => 36, "36" => 73, "73" => 120);
foreach ($iters as $iter1 => $iter2) {
  $ct = 0;
  if ($iter1 != 0)
    echo '</div><div class="list_deptmts dept_col'.$iter1.'">';
  foreach($circos as $num => $dept) {
    $ct++;
    if ($ct <= $iter1)
      continue;
    if (preg_match('/^\d$/', $num)) $num = sprintf("%02d",$num);
    $num = strtoupper($num);
?>
  <span class="dept dep_map" id="dep<?php echo $num; ?>">
    <a href="<?php echo url_for('@list_parlementaires_departement?departement='.preg_replace('/ /', '_', $dept)); ?>">
      <span class="deptnum"><?php echo $num; ?> &ndash;&nbsp;</span>
      <span class="deptnom"><?php echo $dept; ?></span>
    </a>
  </span>
<?php if ($ct == $iter2)
    break;
  }
} ?>
</div>
</div>
