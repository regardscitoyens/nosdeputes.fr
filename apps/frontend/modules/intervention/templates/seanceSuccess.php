<div class="titre_int_et_seance" id="sommaire">
<?php if ($seance->type == 'commission') : ?>
<h1><?php echo link_to($orga->getNom(), '@list_parlementaires_organisme?slug='.$orga->getSlug()); ?></h1>
<h2><?php echo $seance->getTitre(); ?></h2>
<p><?php echo link_to('->Députés Présents', '@presents_seance?seance='.$seance->id); ?></p>
<?php $plot = 'seance_com_'; else :?>
<h1><?php echo $seance->getTitre(0,1); ?></h1>
<?php $plot = 'seance_hemi_'; endif; ?>
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
<?php echo include_component('plot', 'groupes', array('plot' => $plot.$seance->id)); ?>
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
        echo '<h2 class="section">'.link_to(ucfirst($intervention->Section->Section->titre),'@section?id='.$intervention->Section->Section->id);
	echo '<a href="#sommaire">^^</a>';
	echo '</h2>';
      }
      if ($intervention->Section->id != $intervention->Section->section_id) {
        echo '<h3 class="sous-section">';
        echo link_to(ucfirst($intervention->Section->titre),'@section?id='.$intervention->Section->id);
	echo '<a href="#sommaire">^^</a>';
	echo '</h3>';
      }
    }
  } else echo '<div class="intervenant">';
    if ($intervention->hasIntervenant()) {
      $didascalie = 0;
      $perso = $intervention->getIntervenant();
      if ($titre != 1) echo '<span class="source"><a href="'.url_for("@interventions_seance?seance=$seance->id").'#inter_'.$intervention->getMd5().'">permalink</a></span>';
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
      <span id='com_<?php echo $intervention->id; ?>'><a href="<?php echo url_for('@intervention?id='.$intervention->id); ?>#commentaires">Voir tous les commentaires</a> - </span><span><a href="<?php echo url_for('@intervention?id='.$intervention->id); ?>#ecrire">Laisser un commentaire</a></span>
    </div>
  <?php endif; ?>
  </div>
  <?php endforeach; ?>
</div>
<script>
$(document).ready(function() {
    $.ajax({
      url: "<?php echo url_for('@seance_commentaires_json?seance='.$seance->id); ?>",
      success: function(html){
	  ids = eval('(' +html+')');
	  for(i in ids) {
	    if (ids[i] == 0) {
	      $('#com_'+i).text('');
	    }else if (ids[i] == 1) {
	      $('#com_'+i+' a').text("Voir le commentaire");
	    }else {
	      $('#com_'+i+' a').text("Voir les "+ids[i]+" commentaires");
	    }
	  }
  }
});

  })
</script>
