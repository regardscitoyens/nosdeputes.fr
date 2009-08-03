<div class="titre_int_et_seance">
<?php if ($seance->type == 'commission') : ?>
<h1><?php echo $seance->getOrganisme()->getNom(); ?></h1>
<h2>Séance du <?php echo $seance->getDate() ?> à <?php echo $seance->getMoment(); ?></h2>
<?php else :?>
<h1>Séance en hémicycle</h1>
<h2>du <?php echo $seance->getDate() ?> à <?php echo $seance->getMoment(); ?></h2>
<?php endif; ?>
<ul>
<?php foreach($seance->getTableMatiere() as $table) : if (!$table['titre']) {continue;} ;?>
<?php if ($table['section_id']) echo '<ul>'; ?>
<li><a href="#table_<?php echo $table['id']; ?>"><?php echo $table['titre']; ?></a></li>
<?php if ($table['section_id']) echo '</ul>'; ?>
<?php endforeach; ?>
</ul>
</div>
<div class="interventions">
  <?php $table = ''; foreach($interventions as $intervention) : ?>
  <div class="intervention" id="<?php echo $intervention->getId(); ?>">
  <?php
  if ($table != $intervention->getSectionId()) {
    $table = $intervention->getSectionId();
    echo '<div class="intervenant" id="table_'.$table.'">';
  }
  else { echo '<div class="intervenant">'; }
  $persos = $intervention->getAllPersonnalitesAndFonctions(); 
  
  if (count($persos)) {
    $didascalie = 0;
    
    if ($persos[0][0]->getPageLink()) {
    
      if ($persos[0][0]->getPhoto()) {
        echo '<a href="'.url_for($persos[0][0]->getPageLink()).'"><img alt="Photo de '.$persos[0][0]->nom.'" src="'.$persos[0][0]->getPhoto().'" /></a>';
      }
    echo '<span class="perso"><a href="'.url_for($persos[0][0]->getPageLink()).'">';
    echo $persos[0][0]->nom;
    
      if (isset($persos[0][1])) {
        echo ', '.$persos[0][1];
      }
    echo '</a>&nbsp;:</span>';
    }
    else {
      echo '<span class="perso">'.$persos[0][0]->nom;
      
      if (isset($persos[0][1])) {
        echo ', '.$persos[0][1];
      }
      echo '</span>';
    }
    echo '<span class="source"><a href="'.$intervention->getSource().'">source</a> - <a href="'.url_for("@interventions_seance?seance=$seance->id").'#'.$intervention->getId().'">permalink</a></span>';
  }
  else {
    $didascalie = 1;
    echo '<strong>Didascalie : </strong>';
    echo '<span class="source"><a href="'.$intervention->getSource().'">source</a> - <a href="'.url_for("@interventions_seance?seance=$seance->id").'#'.$intervention->getId().'">permalink</a></span>';
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
  </div>
  <?php endforeach; ?>
</div>