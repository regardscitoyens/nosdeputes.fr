<div class="amendement" id="<?php echo $amendement->getSource(); ?>">
  <h2>Projet/Proposition de loi N° <?php echo $amendement->getTexteloi_id(); ?> : //titre//</h2>
  <h1><?php echo $amendement->getTitre(); ?></h1>
  <p class="source"><a href="<?php echo $amendement->getLink(); ?>">source</a> - <a href="<?php echo $amendement->getLinkPDF(); ?>">PDF</a></p>
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
  <div class="commentaires">
    3 commentaires dont celui de zouze :
    Cet amendement tue des gnous !
  </div>
</div>