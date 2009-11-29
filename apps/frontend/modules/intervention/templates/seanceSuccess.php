<div class="titre_int_et_seance" id="sommaire">
<?php if ($seance->type == 'commission') : ?>
<h1><?php echo link_to($orga->getNom(), '@list_parlementaires_organisme?slug='.$orga->getSlug()); ?></h1>
<h1><?php echo $seance->getTitre(); ?></h1>
<?php $sf_response->setTitle($orga->getNom().' : '.$seance->getTitre()); ?>
<?php $plot = 'seance_com_'; else :?>
<h1><?php echo $seance->getTitre(0,1); $sf_response->setTitle($seance->getTitre(0,1).' : NosDeputes.fr'); ?></h1>
<?php $plot = 'seance_hemi_'; endif; ?>
<div class="resume">
<h2>Résumé de la séance</h2>
<?php if (count($tags)) { ?>
<div class="nuage_de_tags">
<h3>Les mots clés de cette séance</h3>
<ul><?php foreach(array_keys($tags) as $tag) echo "<li>$tag</li>"; ?></ul>
</div>
<?php } ?>
<div class="plot_seance">
<?php echo include_component('plot', 'groupes', array('plot' => $plot.$seance->id)); ?>
</div>
</div>
</div>
<?php $table_m = $seance->getTableMatiere(); if (count($table_m)) {?>
<div class="orga_dossier">
<h2>Sommaire</h2>
<ul><?php foreach($table_m as $table) : if (!$table['titre']) {continue;} ;?>
<?php if ($table['section_id'] != $table['id']) echo '<ul>'; ?>
<li><?php if (isset($table['id']) && $table['id']) { ?>
<a href="#table_<?php 
echo $table['id']; 
?>"><?php 
echo ucfirst($table['titre']); 
?></a> <?php }
if ($table['nb_interventions']) echo '<span class="dossier">('.link_to('voir le dossier', '@section?id='.$table['id']).')</span>'; ?></li>
<?php if ($table['section_id'] != $table['id']) echo '</ul>'; ?>
<?php endforeach; ?>
</ul>
</div><?php } ?>
<h2>La séance</h2>
<div class="interventions">
  <?php if (!count($interventions)) { ?>
  <p><em>Nous n'avons pas encore pu récupérer le contenu de cette séance, veuillez nous en excuser</em></p>
  <?php } else { $table = ''; $titre = 0; $source_displayed = 0; 
foreach($interventions as $intervention) : 	
if (! $source_displayed) {
	echo '<p class="source"><a href="'.$intervention->source.'" rel="nofollow">Source</a></p><div class="clear"></div>';
	$source_displayed = 1;
}
?>
  <div class="intervention" id="inter_<?php echo $intervention->getMd5(); ?>">
  <?php
  $lasttitre = $titre;
  if ($table != $intervention->getSectionId()) {
    if ($table == '') $titre = 2;
    else $titre = 1;
    $table = $intervention->getSectionId();
  } else $titre = 0;
if ($intervention->getSectionId() && !$intervention->Section->titre) {
  $titre = 0;
 }
  if ($titre != 0) {
    echo '<div id="table_'.$table.'">';
    echo '<span class="source"><a href="#sommaire">Retour au sommaire</a>&nbsp;-&nbsp<a href="#table_'.$intervention->section_id.'">Permalien</a></span><br/>';
    if ($titre != 2) {
      if ($lasttitre != 1) {
        echo '<h2 class="section">'.link_to(ucfirst($intervention->Section->Section->titre),'@section?id='.$intervention->Section->Section->id);
	echo '</h2>';
      }
      if ($intervention->Section->id != $intervention->Section->section_id) {
        echo '<h3 class="sous-section">';
        echo link_to(ucfirst($intervention->Section->titre),'@section?id='.$intervention->Section->id);
	echo '</h3><br/>';
      }
    }
  } else echo '<div class="intervenant">';
    if ($intervention->hasIntervenant()) {
      $didascalie = 0;
      $perso = $intervention->getIntervenant();
      if ($titre != 1) 
	echo '<span class="source"><a href="#table_'.$intervention->section_id.'">Debut de section</a>&nbsp;-&nbsp;<a href="'.url_for("@interventions_seance?seance=$seance->id").'#inter_'.$intervention->getMd5().'">Permalien</a></span>';
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
<?php
  if (!($didascalie && $titre != 0)) { ?>
    <div class="texte_intervention">
    <?php if ($didascalie) echo '<em>'; ?>
    <?php echo $intervention->getIntervention(array('linkify_amendements'=>url_for('@find_amendements_by_loi_and_numero?loi=LLL&numero=AAA'))); ?>
    <?php if ($didascalie) echo '</em>'; ?>
    </div>
  <?php } ?>
<?php if (!$didascalie) : ?>
    <div class="commentaires" id='com_<?php echo $intervention->id; ?>' style="clear: both;">
      <span class="com_link" id="com_link_<?php echo $intervention->id; ?>"><a href="<?php echo url_for('@intervention?id='.$intervention->id); ?>#commentaires">Voir tous les commentaires</a> - </span><span><a href="<?php echo url_for('@intervention?id='.$intervention->id); ?>#ecrire">Laisser un commentaire</a></span>
    </div>
  <?php endif; ?>
  </div></div>
  <?php endforeach; } ?>
</div>

<script>
nbCommentairesCB = function(html){
	  ids = eval('(' +html+')');
          $('.com_link').hide();
	  for(i in ids) {
            if (i < 0)
               continue;
	    if (ids[i] == 0) {
	      $('#com_link_'+i).text('');
	    }else if (ids[i] == 1) {
	      $('#com_link_'+i+' a').text("Voir le commentaire");
	    }else {
	      $('#com_link_'+i+' a').text("Voir les "+ids[i]+" commentaires");
	    }
            $('#com_link_'+i).show();
	  }};
additional_load = function() {
    $.ajax({
      url: "<?php echo url_for('@seance_commentaires_json?seance='.$seance->id); ?>",
      success: nbCommentairesCB,
      error: nbCommentairesCB
      });
    $(".commentaires a").bind('click', function() {
	var c = $(this).parent().parent();
	c.html('<p class="loading"> &nbsp; </p>');
	id = c.attr('id').replace('com_', '');
        showcommentaire = function(html) {
            c.html(html);
	    setTimeout(function() {$('#com_ajax_'+id).slideDown("slow")}, 100);
	  };
        commentaireUrl = "<?php echo url_for('@intervention_commentaires?id=XXX'); ?>".replace('XXX', id);
	$.ajax({url: commentaireUrl,
               success: showcommentaire,
               error: showcommentaire});
	return false;
      });
  };
</script>
