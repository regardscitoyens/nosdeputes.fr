<div class="amendement" id="L<?php echo $amendement->texteloi_id; ?>A<?php echo $amendement->numero; ?>">
  <h2>Projet/Proposition de loi N° <?php echo $amendement->texteloi_id; ?> : //titre//</h2>
<h1><?php echo $amendement->getTitre(); if (count($identiques) > 1 )echo ' (ou identiques)';?></h1>
  <p class="source"><a href="<?php echo $amendement->source; ?>">source</a> - <a href="<?php echo $amendement->getLinkPDF(); ?>">PDF</a></p>
  <div class="signataires">
  <p>Déposé le <?php echo $amendement->date; ?> par : <?php echo $amendement->signataires; ?>.</p>
  <?php 
  $deputes = $amendement->getParlementaires();
  echo '<p align-style="center">';
  foreach ($deputes as $depute) {
    $titre = $depute->nom.', '.$depute->getGroupe()->getNom();
    echo '<a href="'.url_for($depute->getPageLink()).'"><img width="50" height="64" title="'.$titre.'" alt="'.$titre.'" src="'.$depute->getPhoto().'" /></a>&nbsp;';
  }
  echo '</p>';
  ?></div>
  <div class="sort">
    <p>Sort en séance : <?php echo $amendement->getSort(); ?></p>
  </div>
  <div class="sujet">
    <h2><?php echo $amendement->getSujet(); ?></h2>
  </div>
  <div class="texte_intervention">
    <?php echo $amendement->getTexte(); ?>
  </div>
  <div class="expose_amendement">
    <h3>Exposé Sommaire :</h3>
    <?php echo $amendement->getExpose(); ?>
  </div>
 <?php if (count($identiques) > 1) { ?>
<h3>Amendements identiques</h3>
<ul>
<?php foreach($identiques as $identique) { ?>
<?php if ($identique->numero != $amendement->numero) { ?>
<li><?php echo link_to($identique->numero, '@amendement?id='.$identique->id); ?></li>
<?php } }?>
</ul>
<?php }?>
<?php if ($seances) : ?>
<div>
<h3>En séance</h3>
<ul>
<?php foreach($seances as $s) : ?>
<li><a href="<?php echo url_for('@interventions_seance?seance='.$s['seance_id']); ?>#amend_<?php echo $amendement->numero; ?>"><?php echo $s['date']; ?></a></li>
<?php endforeach ?>
</ul>
</div>
<?php endif; ?>
 <div class="commentaires">
    3 commentaires dont celui de zouze :
    Cet amendement tue des gnous !
  </div>
</div>