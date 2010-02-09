<?php use_helper('Text');
if (isset($loi)) $titre = preg_replace('/Simplifions la loi 2\.0 : (.*)\s*<br.*$/', '\1', $loi);
else $titre = 'Projet de loi N°&nbsp;'.$amendement->texteloi_id; ?>
  <div class="amendement" id="<?php echo $amendement->id; ?>">
    <strong><h3><?php echo link_to(myTools::displayShortDate($amendement->date).' &mdash; '.$titre.' - '.$amendement->sujet.' : '.$amendement->getTitreNoLink().' ('.preg_replace('/indéfini/i', 'Sort indéfini', $amendement->getSort()).')', 'amendement/show?id='.$amendement->id); ?><br/>
    <?php echo link_to(truncate_text($amendement->getSignataires(), 120), 'amendement/show?id='.$amendement->id); ?></h3></strong>
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
      <p><?php echo link_to("Voir tout l'amendement", 'amendement/show?id='.$amendement->id);
        if ($amendement->nb_commentaires) {
          echo ' &mdash; ';
          $titre = 'Voir le';
          if ($amendement->nb_commentaires > 1) $titre .= 's '.$amendement->nb_commentaires;
          $titre .= ' commentaire';
          if ($amendement->nb_commentaires > 1) $titre .= 's';
          echo link_to($titre, 'amendement/show?id='.$amendement->id.'#commentaires');
        } ?></p>
    </div>
  </div>
