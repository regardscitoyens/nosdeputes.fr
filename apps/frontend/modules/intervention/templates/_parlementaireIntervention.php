<?php use_helper('Text') ?>
  <div class="intervention" id="<?php echo $intervention->id; ?>">
  <div>
  <?php
  $link_seance = url_for('@interventions_seance?seance='.$intervention->getSeance()->id).'#inter_'.$intervention->getMd5();
  if (!isset($complete)) { //Si on vient d'une recherche et non d'un page intervention
    $section = $intervention->getSection();
    $titre2 = '<a href="'.$link_seance.'">'.$intervention->getSeance()->getTitre().' ';
    if ($intervention->getType() == 'commission') {
      $orga = $intervention->getSeance()->getOrganisme();
      if (!isset($complete))
	$titre2 .= $orga->getNom().'</a>';
    } else {
      $titre2 .= "&nbsp;: ";
      if ($section->getSection())
	if ($section->getSection()->getTitre()) {
	  $titre2 .= ucfirst($section->getSection()->getTitre());
	  $titre2 .= ', ';
	}
      $titre2 .= ucfirst($section->getTitre()).'</a>';
    }
    echo '<h3>'.$titre2.'</h3>';
  }
    if (isset($complete)) 
      echo '<span class="source"><a href="'.$intervention->getSource().'">source</a>'; 
?></div>
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
  else $p_inter = truncate_text($inter, 350);
 }
if ($intervention->hasIntervenant()) {
  $perso = $intervention->getIntervenant();
  if (!isset($nophoto)) {
    if (isset($complete)) {
      if (!($link = url_for($perso->getPageLink()))) 
	$link = $link_seance;
    } else $link = $link_seance;
    if ($perso->getPageLink()) {
      if ($perso->hasPhoto()) {
        echo '<a href="'.$link.'" class="intervenant"><img width="50" height="70" alt="'.$perso->nom.'" src="'.url_for('@resized_photo_parlementaire?height=64&slug='.$perso->slug).'" /></a>';
      }
      echo '<a href="'.$link.'">';
      echo $intervention->getNomAndFonction();
      echo '</a>&nbsp;:';
    }
    else {
      echo '<span class="perso">'.$perso->nom.'&nbsp;:';
      echo '</span>';
    }
  }
 }
  echo '<p>'.$p_inter.'</p>';
?></div>
    <div class="contexte">
    <p><?php echo link_to("Voir dans le contexte", $link_seance);
    if (!isset($complete) && $intervention->nb_commentaires) {
      if ($intervention->nb_commentaires == 1) $commenttitre = 'Voir le commentaire';
      else $commenttitre = 'Voir les commentaires';
      echo ' &mdash; '.link_to($commenttitre, '/intervention/'.$intervention->id.'#commentaires'); } ?></p>
    </div>
      <?php if (isset($complete)) { ?>
    <div id="commentaires">
<?php echo include_component('commentaire', 'show', array('object'=>$intervention));
      echo include_component('commentaire', 'form', array('object'=>$intervention)); ?>
    </div>
  <?php } ?>
  </div>
