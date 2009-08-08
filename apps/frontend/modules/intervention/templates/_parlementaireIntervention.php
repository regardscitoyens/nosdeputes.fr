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
if ($intervention->hasIntervenant()) {
  $perso = $intervention->getIntervenant();
  $didascalie = 0;
  echo '<span class="source"><a href="'.$intervention->getSource().'">source</a> - <a href="'.url_for("@interventions_seance?seance=$intervention->seance_id").'#'.$intervention->getId().'">permalink</a></span>';
  if ($perso->getPageLink()) {
    
    if ($perso->getPhoto()) {
      echo '<img width="50" height="64" alt="'.$perso->nom.'" src="'.$perso->getPhoto().'" />';
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
  echo '<p>'.$p_inter.'</p>';
?></div>
    <div class="plus">
      3 commentaires - <a href="<?php echo url_for('@interventions_seance?seance='.$intervention->getSeance()->id); ?>#inter_<?php echo $intervention->getMd5(); ?>">Voir l'intervention dans son contexte</a>
    </div>
  </div>
