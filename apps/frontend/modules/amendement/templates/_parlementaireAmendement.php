<?php use_helper('Text') ?>
  <div class="amendement" id="<?php echo $amendement->id; ?>">
    <div class="info">
    <p><?php echo $amendement->date; ?> <strong>Loi N° <?php echo $amendement->texteloi_id; ?> : <?php echo link_to($amendement->getTitreNoLink(), 'amendement/show?id='.$amendement->id); ?></strong> (<?php echo $amendement->sort; ?>)<br>
    <?php echo truncate_text($amendement->getSignataires(), 120); ?></p>
    <p class="source"><a href="<?php echo $amendement->source; ?>">source</a> - <a href="<?php echo $amendement->getLinkPDF(); ?>">PDF</a></p>
    </div>
    <div class="texte_amendement"><?php
$amdmt = preg_replace('/<br\/?>|<\/?p>|\&[^\;]+\;/i', ' ', $amendement->getTexte(0)." Exposé sommaire : ".$amendement->getExpose());
$p_amdmt = '';
if (isset($highlight)) {
  foreach ($highlight as $h) {
    $p_amdmt .= excerpt_text($amdmt, $h, 400/count($highlight));
  }
  foreach ($highlight as $h) {
    $p_amdmt = highlight_text($p_amdmt, $h);
  }
}
if ($p_amdmt == '')
  $p_amdmt = truncate_text($amdmt, 400);
echo '<p>'.$p_amdmt.'</p>';
?></div>
    <div class="plus">
      3 commentaires
    </div>
  </div>
