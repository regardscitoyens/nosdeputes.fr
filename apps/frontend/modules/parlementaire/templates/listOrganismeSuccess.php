<h1><?php echo $orga->getNom(); $sf_response->setTitle($orga->getNom()); ?></h1>
<?php include_component('article', 'show', array('categorie'=>'Organisme', 'object_id'=>$orga->id)); ?>
<?php if ($total && $pagerSeances->getPage() < 2) {
  if ($orga->type == 'extra') : ?>
<h2>Organisme extra-parlementaire composé de <?php echo $total; ?> député<?php if ($total > 1) echo 's'; ?>&nbsp;:</h2>
<?php else : ?>
<h2><?php if (preg_match('/commission/i', $orga->getNom())) echo 'Comm'; else echo 'M'; ?>ission parlementaire composée de <?php echo $total; ?> député<?php if ($total > 1) echo 's'; ?>&nbsp;:</h2>
<?php endif; ?>
<div class="liste">
<?php $listimp = array_keys($parlementaires);
  foreach($listimp as $i) {
    echo '<div class="list_table">';
    include_partial('parlementaire/table', array('deputes' => $parlementaires[$i], 'list' => 1, 'imp' => $i));
    echo '</div>';
  } 
  echo '</div>';
} 
if ($pagerSeances->getNbResults()) : ?>
<div><h3>Les dernières réunions de la <?php if (preg_match('/commission/i', $orga->getNom())) echo 'Comm'; else echo 'M'; ?>ission</h3>
<ul>
<?php foreach($pagerSeances->getResults() as $seance) : ?>
<li><?php $subtitre = $seance->getTitre();
  if ($seance->nb_commentaires > 0) {
    $subtitre .= ' ('.$seance->nb_commentaires.' commentaire';
    if ($seance->nb_commentaires > 1) $subtitre .= 's';
    $subtitre .= ')';
  }
  echo link_to($subtitre, '@interventions_seance?seance='.$seance->id); ?></li>
<?php endforeach ; ?>
</ul>
<?php include_partial('intervention/paginate', array('pager'=>$pagerSeances, 'link'=>'@list_parlementaires_organisme?slug='.$orga->getSlug().'&')); ?>
</div>
<?php endif; ?>
<br/>
