<h1 class="list_inter">Toutes les circonscriptions par département</h1><?php 
$sf_response->setTitle('Toutes les circonscriptions électorales par département - NosDéputés.fr'); ?>
<?php // CirconscriptionActions::echoCircoMap("full", 900, 0); ?>
<div class="list_circo">
<?php $div = floor(count($circos)/6)+1; $ct = 0; 
foreach($circos as $num => $circo) {
  $ct++;
  if (preg_match('/^\d$/', $num)) $num = sprintf("%02d",$num);
  echo '<p onclick="document.location=\''.url_for('@list_parlementaires_departement?departement='.preg_replace('/ /', '_', $circo)).'\'" class="dept" id="dep'.$num.'">'.link_to($circo, '@list_parlementaires_departement?departement='.preg_replace('/ /', '_', $circo)).'</p>';
  if ($ct == 27)
    break; 
} ?>
</div>
<?php echo include_partial('circonscription/mapDeptmts', array()); ?>
<div class="list_circo borderleft">
<?php $ct = 0;
foreach($circos as $num => $circo) {
  $ct++;
  if ($ct <= 27)
    continue;
  if (preg_match('/^\d$/', $num)) $num = sprintf("%02d",$num);
  echo '<p onclick="document.location=\''.url_for('@list_parlementaires_departement?departement='.preg_replace('/ /', '_', $circo)).'\'" class="dept" id="dep'.$num.'">'.link_to($circo, '@list_parlementaires_departement?departement='.preg_replace('/ /', '_', $circo)).'</p>';
  if ($ct == 55)
    break; 
} ?>
</div>
<div class="list_circo borderleft">
<?php $ct = 0;
foreach($circos as $num => $circo) {
  $ct++;
  if ($ct <= 55)
    continue;
  if (preg_match('/^\d$/', $num)) $num = sprintf("%02d",$num);
  echo '<p onclick="document.location=\''.url_for('@list_parlementaires_departement?departement='.preg_replace('/ /', '_', $circo)).'\'" class="dept" id="dep'.$num.'">'.link_to($circo, '@list_parlementaires_departement?departement='.preg_replace('/ /', '_', $circo)).'</p>';
  if ($ct == 66)
    break;
} ?>
</div>
<div class="list_circo borderleft">
<?php $ct = 0;
foreach($circos as $num => $circo) {
  $ct++;
  if ($ct <= 66)
    continue;
  if (preg_match('/^\d$/', $num)) $num = sprintf("%02d",$num);
  echo '<p onclick="document.location=\''.url_for('@list_parlementaires_departement?departement='.preg_replace('/ /', '_', $circo)).'\'" class="dept" id="dep'.$num.'">'.link_to($circo, '@list_parlementaires_departement?departement='.preg_replace('/ /', '_', $circo)).'</p>';
  if ($ct == 77)
    break;
} ?>
</div>
<div class="list_circo borderleft">
<?php $ct = 0;
foreach($circos as $num => $circo) {
  $ct++;
  if ($ct <= 77)
    continue;
  if (preg_match('/^\d$/', $num)) $num = sprintf("%02d",$num);
  echo '<p onclick="document.location=\''.url_for('@list_parlementaires_departement?departement='.preg_replace('/ /', '_', $circo)).'\'" class="dept" id="dep'.$num.'">'.link_to($circo, '@list_parlementaires_departement?departement='.preg_replace('/ /', '_', $circo)).'</p>';
  if ($ct == 88)
    break;
} ?>
</div>
<div class="list_circo borderleft">
<?php $ct = 0;
foreach($circos as $num => $circo) {
  $ct++;
  if ($ct <= 88)
    continue;
  if (preg_match('/^\d$/', $num)) $num = sprintf("%02d",$num);
  echo '<p onclick="document.location=\''.url_for('@list_parlementaires_departement?departement='.preg_replace('/ /', '_', $circo)).'\'" class="dept" id="dep'.$num.'">'.link_to($circo, '@list_parlementaires_departement?departement='.preg_replace('/ /', '_', $circo)).'</p>';
  if ($ct == 96)
    break;
} ?>
</div>
<div class="list_circo borderleft">
<h3 class="align_center">DOM-TOMs&nbsp;:</h3>
<?php $ct = 0;
foreach($circos as $num => $circo) {
  $ct++;
  if ($ct <= 96)
    continue;
  if (preg_match('/^\d$/', $num)) $num = sprintf("%02d",$num);
  echo '<p onclick="document.location=\''.url_for('@list_parlementaires_departement?departement='.preg_replace('/ /', '_', $circo)).'\'" class="dept" id="dep'.$num.'">'.link_to($circo, '@list_parlementaires_departement?departement='.preg_replace('/ /', '_', $circo)).'</p>';
 } ?>
</div>

