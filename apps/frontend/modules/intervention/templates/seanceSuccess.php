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
<?php if ($table['section_id'] != $table['id']) echo '<ul>'; ?>
<li><a href="#table_<?php echo $table['id']; ?>"><?php echo $table['titre']; ?></a> (<?php echo link_to('voir le dossier', '@section?id='.$table['id']); ?>)</li>
<?php if ($table['section_id'] != $table['id']) echo '</ul>'; ?>
<?php endforeach; ?>
</ul>
<ul>
<?php
   foreach(array_keys($tags) as $tag) {
   echo "<li>$tag</li>";
 }
?></ul>
</div>
<div class="interventions">
  <?php $table = ''; foreach($interventions as $intervention) : ?>
  <div class="intervention" id="inter_<?php echo $intervention->getMd5(); ?>">
  <?php
  if ($table != $intervention->getSectionId()) {
    $table = $intervention->getSectionId();
    echo '<div class="intervenant" id="table_'.$table.'">';
  }
  else { echo '<div class="intervenant">'; }
  if ($intervention->hasIntervenant()) {
    $didascalie = 0;
    $perso = $intervention->getIntervenant();

    if ($perso->getPageLink()) {	
      if ($photo = $perso->getPhoto()) {
	echo '<a href="'.$photo.'"><img alt="Photo de '.$perso->nom.'" src="'.$perso->getPhoto().'" /></a>';
      }
      echo '<span class="perso"><a href="'.url_for($perso->getPageLink()).'">';
      echo $intervention->getNomAndFonction();
      echo '</a></span>';
    }else {
      echo '<span class="perso">'.$intervention->getNomAndFonction().'</span>';
    }
    echo '<span class="source"><a href="'.$intervention->getSource().'">source</a> - <a href="'.url_for("@interventions_seance?seance=$seance->id").'#inter_'.$intervention->getMd5().'">permalink</a></span>';
  }
  else {
    $didascalie = 1;
    echo '<strong>Didascalie : </strong>';
    echo '<span class="source"><a href="'.$intervention->getSource().'">source</a> - <a href="'.url_for("@interventions_seance?seance=$seance->id").'#'.$intervention->getId().'">permalink</a></span>';
  }
  ?></div>
    <div class="texte_intervention">
    <?php echo $intervention->getIntervention(array('linkify_amendements'=>url_for('@search_amendements_by_loi_and_numero?loi=LLL&numero=AAA'))); ?>
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