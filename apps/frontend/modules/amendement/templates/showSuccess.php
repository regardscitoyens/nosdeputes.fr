<div class="temp">
<div class="amendement" id="L<?php echo $amendement->texteloi_id; ?>A<?php echo $amendement->numero; ?>">
<h1><?php echo ucfirst($section->titre).' ('.link_to('voir le dossier', '@section?id='.$section->id).') '; ?></h1>
<h1><?php echo $amendement->getTitre().' ('.$amendement->getSort().')'; ?></h1>
  <p class="source"><a href="<?php echo $amendement->source; ?>">source</a> - <a href="<?php echo $amendement->getLinkPDF(); ?>">PDF</a></p>
  <div class="identiques">
  <?php if (count($identiques) > 1) : ?>
  <?php if (count($identiques) > 2) { $ident_titre = "( Amendements identiques : "; } else { $ident_titre = "( Amendement identique : "; } ?>
  <p><em><?php echo $ident_titre; foreach($identiques as $identique) { if ($identique->numero != $amendement->numero) {
      echo link_to($identique->numero, '@amendement?id='.$identique->id)." "; } } ?>)</em></p>
  <?php endif; ?>
  </div>
  <div class="signataires">
  <p>Déposé le <?php echo myTools::displayDate($amendement->date); ?> par : <?php echo $amendement->signataires; ?>.</p>
  <?php 
  $deputes = $amendement->getParlementaires(); ?>
  <div class="photos"><p>
  <?php foreach ($deputes as $depute) {
    $titre = $depute->nom.', '.$depute->groupe_acronyme;
    echo '<a href="'.url_for($depute->getPageLink()).'"><img width="50" height="64" title="'.$titre.'" alt="'.$titre.'" src="'.url_for('@resized_photo_parlementaire?height=70&slug='.$depute->slug).'" /></a>&nbsp;';
  }
  ?></p></div></div>
  <div class="sujet">
    <h2><?php echo $amendement->getSujet().' de la loi N° '.myTools::getLinkLoi($amendement->texteloi_id); ?></h2>
  </div>
  <div class="texte_intervention">
    <?php echo $amendement->getTexte(); ?>
  </div>
  <div class="expose_amendement">
    <h3>Exposé Sommaire :</h3>
    <?php echo $amendement->getExpose(); ?>
  </div>
<?php if ($seances) : ?>
<div>
<h3>En séance</h3>
<ul>
<?php foreach($seances as $s) : ?>
<li><a href="<?php echo url_for('@interventions_seance?seance='.$s['seance_id']); ?>#amend_<?php echo $amendement->numero; ?>"><?php echo myTools::displayDate($s['date']); ?></a></li>
<?php endforeach ?>
</ul>
</div>
<?php endif; ?>
 <div class="commentaires">
 <h3>Commentaires</h3>
<?php echo include_component('commentaire', 'show', array('object' => $amendement)); ?>
<?php echo include_component('commentaire', 'form', array('object' => $amendement)); ?>
  </div>
</div>
</div>
