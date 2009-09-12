<?php use_helper('Text') ?>
  <div class="intervention" id="<?php echo $intervention->id; ?>">
    <div class="info">
    <strong>  
    <?php 
    echo $intervention->getSeance()->getTitre(0,0,$intervention->getMd5()).' - ';
    if ($intervention->getType() == 'commission') { $orga = $intervention->getSeance()->getOrganisme(); echo link_to($orga->getNom(), '@list_parlementaires_organisme?slug='.$orga->getSlug()); }
    else { $section = $intervention->getSection(); 
	    if ($section->getSection()) 
		    echo link_to(ucfirst($section->getSection()->getTitre()),
				    '@section?id='.$section->section_id).' > ';
	    echo link_to(ucfirst($section->titre), '@section?id='.$section->id); }
    ?> 
    </strong>
 <?php if (isset($complete)) echo '<span class="source"><a href="'.$intervention->getSource().'">source</a>'; ?>
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
  if (isset($complete)) $p_inter = $intervention->getIntervention(array('linkify_amendements'=>url_for('@find_amendements_by_loi_and_numero?loi=LLL&numero=AAA')));
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
  <?php if (!isset($complete)) {
    if (!$didascalie) : ?>
    <div class="commentaires" style="clear: both;">
      <span><a href="<?php echo url_for('@intervention?id='.$intervention->id); ?>">Toute l'intervention</a></span> -
      <span><a href="<?php echo url_for('@intervention?id='.$intervention->id); ?>#commentaires">Les commentaires</a></span> -
      <span><a href="<?php echo url_for('@intervention?id='.$intervention->id); ?>#ecrire">Laisser un commentaire</a></span>
    </div>
  <?php endif; } else { ?>
    <p><?php echo link_to("Voir l'intervention dans son contexte", '@interventions_seance?seance='.$intervention->getSeance()->id.'#inter_'.$intervention->getMd5()); ?></p>
  <?php } ?>
  </div>
