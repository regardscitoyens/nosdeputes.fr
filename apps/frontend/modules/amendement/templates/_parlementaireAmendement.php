<?php use_helper('Text');
$titre = $amendement->getTitre();
?>
  <div class="amendement" id="<?php echo $amendement->id; ?>">
    <h3><?php echo link_to(myTools::displayShortDate($amendement->date).' &mdash; '.$titre, '@amendement?loi='.$amendement->texteloi_id.'&numero='.$amendement->numero); ?><br/>
    <?php echo link_to(truncate_text($amendement->getSignataires(), 120), '@amendement?loi='.$amendement->texteloi_id.'&numero='.$amendement->numero); ?></h3>
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
echo '<p>'.myTools::escape_blanks($p_amdmt).'</p>';
?></div>
    <div class="contexte">
  <p><?php echo link_to("Voir tout l'amendement",  '@amendement?loi='.$amendement->texteloi_id.'&numero='.$amendement->numero);
        if ($amendement->nb_commentaires) {
          echo ' &mdash; ';
          $titre = 'Voir le';
          if ($amendement->nb_commentaires > 1) $titre .= 's '.$amendement->nb_commentaires;
          $titre .= ' commentaire';
          if ($amendement->nb_commentaires > 1) $titre .= 's';
          echo link_to($titre, '@amendement?loi='.$amendement->texteloi_id.'&numero='.$amendement->numero.'#commentaires');
        } ?></p>
    </div>
  </div>
