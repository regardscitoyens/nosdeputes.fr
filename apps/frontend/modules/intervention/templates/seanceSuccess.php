<div class="titre_int_et_seance" id="sommaire">
<?php if ($seance->type == 'commission') : ?>
<h1><?php echo link_to($orga->getNom(), '@list_parlementaires_organisme?slug='.$orga->getSlug()); ?></h1>
<h2><?php echo $seance->getTitre(); ?></h2>
<p><?php echo link_to('->Députés Présents', '@presents_seance?seance='.$seance->id); ?></p>
<?php else :?>
<h1><?php echo $seance->getTitre(0,1); ?></h1>
<?php endif; ?>
<ul>
<?php foreach($seance->getTableMatiere() as $table) : if (!$table['titre']) {continue;} ;?>
<?php if ($table['section_id'] != $table['id']) echo '<ul>'; ?>
<li><a href="#table_<?php echo $table['id']; ?>"><?php echo ucfirst($table['titre']); ?></a> <?php if ($table['nb_interventions']) echo '('.link_to('voir le dossier', '@section?id='.$table['id']).') '; ?></li>
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
  <?php $table = ''; $titre = 0; foreach($interventions as $intervention) : ?>
  <div class="intervention" id="inter_<?php echo $intervention->getMd5(); ?>">
  <?php
  $lasttitre = $titre;
  if ($table != $intervention->getSectionId()) {
    if ($table == '') $titre = 2;
    else $titre = 1;
    $table = $intervention->getSectionId();
  } else $titre = 0;
  if ($titre != 0) {
    echo '<div class="intervenant" id="table_'.$table.'">';
    if ($titre != 2) {
      if ($lasttitre != 1) {
        echo '<h2><span class="section">'.ucfirst($intervention->Section->Section->titre).' '.link_to('->dossier','@section?id='.$intervention->Section->Section->id).'</span></h2>';
      }
      if ($intervention->Section->id != $intervention->Section->section_id) {
        echo '<h3><span class="sous-section">';
        echo ucfirst($intervention->Section->titre).' '.link_to('->dossier','@section?id='.$intervention->Section->id).'</span></h3>';
      }
      echo '<a href="#sommaire">^ Retour au sommaire ^</a>';
    }
  } else echo '<div class="intervenant">';
    if ($intervention->hasIntervenant()) {
      $didascalie = 0;
      $perso = $intervention->getIntervenant();
      if ($perso->getPageLink()) {
        if ($photo = $perso->hasPhoto()) {
        echo '<a href="'.url_for($perso->getPageLink()).'"><img alt="Photo de '.$perso->nom.'" src="'.url_for('@resized_photo_parlementaire?height=70&slug='.$perso->slug).'" /></a>';
        }
        echo '<span class="perso"><a href="'.url_for($perso->getPageLink()).'">';
        echo $intervention->getNomAndFonction();
        echo '</a></span>';
      } else {
        echo '<span class="perso">'.$intervention->getNomAndFonction().'</span>';
      }
      if ($titre != 1) echo '<span class="source"><a href="'.$intervention->getSource().'">source</a> - <a href="'.url_for("@interventions_seance?seance=$seance->id").'#inter_'.$intervention->getMd5().'">permalink</a></span>';
    } else {
      $didascalie = 1;
    } ?>
  </div>
<?php
  if (!($didascalie && $titre != 0)) { ?>
    <div class="texte_intervention">
    <?php if ($didascalie) echo '<em>'; ?>
    <?php echo $intervention->getIntervention(array('linkify_amendements'=>url_for('@find_amendements_by_loi_and_numero?loi=LLL&numero=AAA'))); ?>
    <?php if ($didascalie) echo '</em>'; ?>
    </div>
  <?php } 
  if (!$didascalie) : ?>
    <div class="commentaires" style="clear: both;">
      <span><a href="<?php echo url_for('@intervention?id='.$intervention->id); ?>#commentaires">Voir tous les commentaires</a></span> -
      <span><a href="<?php echo url_for('@intervention?id='.$intervention->id); ?>#ecrire">Laisser un commentaire</a></span>
    </div>
  <?php endif; ?>
  </div>
  <?php endforeach; ?>
</div>
