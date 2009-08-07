<?php use_helper('Text') ?>
  <div class="intervention" id="<?php echo $intervention->id; ?>">
    <div class="info">
    <strong>  
    <?php 
    echo $intervention->getSeance()->getDate().' : ';
    
    if ($intervention->getType() == 'commission') { echo $intervention->getSeance()->getOrganisme()->getNom(); }
    else { echo $intervention->getSection()->getTitreComplet(); }
    ?> 
    </strong>
    </div>
    <div class="texte_intervention"><?php 
$inter = preg_replace('/<\/?p>|\&[^\;]+\;/i', ' ', $intervention->getIntervention()); 
if (isset($highlight)) {
  $p_inter = '';
  foreach ($highlight as $h) {
    $p_inter .= excerpt_text($inter, $h, 400/count($highlight));
  }
  foreach ($highlight as $h) {
    $p_inter = highlight_text($p_inter, $h);
  }
}else{
  $p_inter = truncate_text($inter, 400);
}

$persos = $intervention->getAllPersonnalitesAndFonctions(); 
  
if (count($persos)) {
  $didascalie = 0;
  foreach ($persos as $perso) {
    if ($perso[0]->getPageLink()) {
      
      if ($perso[0]->getPhoto()) {
	echo '<a href="'.url_for($perso[0]->getPageLink()).'"><img alt="Photo de '.$perso[0]->nom.'" src="'.$perso[0]->getPhoto().'" /></a>';
      }
      echo '<span class="perso"><a href="'.url_for($perso[0]->getPageLink()).'">';
      echo $perso[0]->nom;
      
      if (isset($perso[1])) {
	echo ', '.$perso[1];
      }
      echo '</a>&nbsp;:</span>';
    }
    else {
      echo '<span class="perso">'.$perso[0]->nom;
      
      if (isset($perso[1])) {
	echo ', '.$perso[1];
      }
      echo '</span>';
    }
  }
 }
echo '<p>'.$p_inter.'</p>';
?></div>
    <div class="plus">
      3 commentaires - <a href="<?php echo url_for('@interventions_seance?seance='.$intervention->getSeance()->id); ?>#inter_<?php echo $intervention->getMd5(); ?>">Voir l'intervention dans son contexte</a>
    </div>
  </div>
