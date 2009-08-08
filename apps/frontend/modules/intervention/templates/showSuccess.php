<div class="intervention" id="<?php echo $intervention->getId(); ?>">
    <div class="intervenant">
  <?php
if ($intervention->hasIntervenant()) {
  $perso = $intervention->getIntervenant();
  $didascalie = 0;
  echo '<span class="source"><a href="'.$intervention->getSource().'">source</a> - <a href="'.url_for("@interventions_seance?seance=$intervention->seance_id").'#'.$intervention->getId().'">permalink</a></span>';
  if ($perso->getPageLink()) {
    
    if ($perso->getPhoto()) {
      echo '<img width="50" height="64" alt="'.$perso->nom.'" src="'.$persos->getPhoto().'" />';
    }
    echo '<a href="'.url_for($perso->getPageLink()).'">';
    echo $perso->nom;
    
    echo '</a>&nbsp;:';
  }
  else {
    echo '<span class="perso">'.$perso->nom;    
    echo '</span>';
  }
  else {
    $didascalie = 1;
    echo '<strong>Didascalie :</strong>';
		echo '<span class="source"><a href="'.$intervention->getSource().'">source</a> - <a href="'.url_for("@interventions_seance?seance=$intervention->seance_id").'#'.$intervention->getId().'">permalink</a></span>';
  }
  ?></div>
    <div class="texte_intervention">
  <?php echo $intervention->getIntervention(); ?>
    </div>
  <?php if (!$didascalie) : ?>
    <div class="commentaires">
      3 commentaires dont celui de toto :
      Cette intervention c'est de la balle !
    </div>
  <?php endif; ?>
    <div class="source">

    </div>
  </div>
