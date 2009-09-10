<?php use_helper('Text') ?>
  <div class="intervention" id="<?php echo $intervention->id; ?>">
    <div class="info">
    <strong>  
    <?php 
    echo myTools::displayDate($intervention->getSeance()->getTitre()).' - ';
    
    if ($intervention->getType() == 'commission') { $orga = $intervention->getSeance()->getOrganisme(); echo link_to($orga->getNom(), '@list_parlementaires_organisme?slug='.$orga->getSlug()); }
    else { $section = $intervention->getSection(); echo link_to(ucfirst($section->titre_complet), '@section?id='.$section->id); }
    ?> 
    </strong>
    </div>
    <div class="texte_intervention"><?php 
$inter = preg_replace('/<\/?p>|\&[^\;]+\;/i', ' ', $intervention->getIntervention()); 
$p_inter = '';
if (isset($highlight)) {
  foreach ($highlight as $h) {
    $p_inter .= excerpt_text($inter, $h, 400/count($highlight));
  }
  foreach ($highlight as $h) {
    if (!preg_match('/'.$h.'/', 'strong class="highlight"/'))
      $p_inter = highlight_text($p_inter, $h);
  }
}
if ($p_inter == '') {
  if (isset($complete)) $p_inter = $inter;
  else  $p_inter = truncate_text($inter, 400);
}
if ($intervention->hasIntervenant()) {
  $perso = $intervention->getIntervenant();
  $didascalie = 0;
  if (!isset($nophoto)) {
    if ($perso->getPageLink()) {
      if ($perso->hasPhoto()) {
        echo '<a href="'.url_for($perso->getPageLink()).'"><img width="50" height="70" alt="'.$perso->nom.'" src="'.url_for('@resized_photo_parlementaire?height=64&slug='.$perso->slug).'" /></a>';
      }
      echo '<a href="'.url_for($perso->getPageLink()).'">';
      echo $perso->nom;

      echo '</a>&nbsp;:';
    }
    else {
      echo '<span class="perso">'.$perso->nom;
      echo '</span>';
    }
  }
 }
  echo '<p>'.$p_inter.'</p>';
?></div>
  <?php if (!$didascalie) : ?>
    <div class="commentaires" style="clear: both;">
      <span><a href="<?php echo url_for('@intervention?id='.$intervention->id); ?>">Toute l'intervention</a></span> -
      <span><a href="<?php echo url_for('@intervention?id='.$intervention->id); ?>#commentaires">Les commentaires</a></span> -
      <span><a href="<?php echo url_for('@intervention?id='.$intervention->id); ?>#ecrire">Laisser un commentaire</a></span>
    </div>
  <?php endif; ?>
  </div>
