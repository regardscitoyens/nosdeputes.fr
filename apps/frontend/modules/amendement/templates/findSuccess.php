<?php $titre = 'Recherche d\'amendements'; ?>
<h1><?php echo $titre; ?></h1>
<?php $sf_response->setTitle($titre);
  if (!isset($amendements))
    echo include_component('amendement', 'pagerAmendements', array('amendement_query' => $amendements_query, 'lois' => $lois));
  else { ?>
<?php if (!count($amendements)) { ?>
<p>Nous n'avons pas trouvé d'amendement correspondant à votre recherche.</p>
<?php } else { ?>
<p><?php $total = count($amendements);
  echo $total;?> amendement<?php if ($total>1) echo 's'; ?> trouvé<?php if ($total>1) echo 's'; ?>&nbsp;:</p>
<ul>
<?php foreach($amendements as $a) :?>
<li><?php echo link_to('Amendement n°'.$a->numero.' portant sur le texte de loi n°'.$a->texteloi_id.$a->getLettreLoi(1).', '.$a->sujet, '@amendement?id='.$a->id); ?></li>
<?php endforeach; ?>
</ul>
<?php } } ?>
