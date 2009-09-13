<?php use_helper('Text') ?>
  <div class="amendement" id="<?php echo $amendement->id; ?>">
    <div class="info">
    <strong><p><?php echo link_to(myTools::displayDate($amendement->date).' &mdash; Texte de loi N° '.$amendement->texteloi_id.'&nbsp;: '.$amendement->getTitreNoLink().' ('.$amendement->sort.')', 'amendement/show?id='.$amendement->id); ?><br/>
    <?php echo link_to(truncate_text($amendement->getSignataires(), 120), 'amendement/show?id='.$amendement->id); ?></p></strong>
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
  $p_amdmt = truncate_text($amdmt, 350);
echo '<p>'.$p_amdmt.'</p>';
?></div>
    <div class="contexte">
      <p><?php echo link_to("Voir tout l'amendement", 'amendement/show?id='.$amendement->id); ?><?php if ($amendement->nb_commentaires) echo ' &mdash; '.link_to('Voir les '.$amendement->nb_commentaires.' commentaires', 'amendement/show?id='.$amendement->id.'#commentaires'); ?></p>
    </div>
  </div>