<?php use_helper('Text') ?>
  <div class="intervention" id="<?php echo $intervention->id; ?>">
    <div class="info">
    <strong>
    <?php $link_seance = url_for('@interventions_seance?seance='.$intervention->getSeance()->id).'#inter_'.$intervention->getMd5();
      if (!isset($complete)) echo '<a href="'.$link_seance.'">'.$intervention->getSeance()->getTitre();
      else echo $intervention->getSeance()->getTitre(0,0,$intervention->getMd5());
      echo ' &mdash; ';
      if ($intervention->getType() == 'commission') {
        $orga = $intervention->getSeance()->getOrganisme();
        if (isset($complete))
          echo link_to($orga->getNom(), '@list_parlementaires_organisme?slug='.$orga->getSlug());
        else echo $orga->getNom().'</a>';
      } else {
        $section = $intervention->getSection();
	    if ($section->getSection()) {
          if (isset($complete))
            echo link_to(ucfirst($section->getSection()->getTitre()), '@section?id='.$section->section_id);
          else echo ucfirst($section->getSection()->getTitre());
          echo '&nbsp;: ';
        }
        if (isset($complete)) {
          echo link_to(ucfirst($section->getTitre()), '@section?id='.$section->id);
          if(count($amdmts) >= 1)
            echo ', amendement';
            if(count($amdmts) > 1) echo 's';
            echo ' ';
            foreach($amdmts as $amdmt)
              echo link_to($amdmt, '/amendements/'.(implode(',',$lois).'/'.$amdmt));
        } else echo ucfirst($section->getTitre()).'</a>';
    }
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
  else $p_inter = truncate_text($inter, 350);
}
if ($intervention->hasIntervenant()) {
  $perso = $intervention->getIntervenant();
  if (!isset($nophoto)) {
    if (isset($complete)) {
      if (!($link = url_for($perso->getPageLink()))) $link = $link_seance;
    } else $link = $link_seance;
    if ($perso->getPageLink()) {
      if ($perso->hasPhoto()) {
        echo '<a href="'.$link.'"><img width="50" height="70" alt="'.$perso->nom.'" src="'.url_for('@resized_photo_parlementaire?height=64&slug='.$perso->slug).'" /></a>';
      }
      echo '<a href="'.$link.'">';
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
  <?php if (isset($complete)) { ?>
    <div class="contexte">
      <p><?php echo link_to("Voir l'intervention dans son contexte", $link_seance); ?></p>
    </div>
    <?php
    echo include_component('commentaire', 'show', array('object'=>$intervention));
    echo include_component('commentaire', 'form', array('object'=>$intervention));
    ?>
  <?php } ?>
  </div>
