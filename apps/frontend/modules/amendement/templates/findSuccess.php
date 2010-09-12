<?php $titre = 'Recherche d\'amendements'; ?>
<h1><?php echo $titre; ?></h1>
<?php $sf_response->setTitle($titre);
  if (!isset($amendements)) {
    if ($sf_request->getParameter('numero') == 'all' || $sf_request->getParameter('numero') == 'new') {
	echo "<p>Triés par : ";
	if ($sf_request->getParameter('numero') == 'new') {
	echo link_to("numéro d'attribution", "@find_amendements_by_loi_and_numero?loi=".implode(',', $lois)."&numero=all");
	echo " - <b>date de dépôt</b>";
	}else{
	echo "<b>numéro d'attribution</b>";
	echo " - ";
	echo link_to("date de dépôt", "@find_amendements_by_loi_and_numero?loi=".implode(',', $lois)."&numero=new");
	}
	echo "</p>";
    }
    $options = array('amendement_query' => $amendements_query, 'lois' => $lois);
    if (isset($loi)) $options = array_merge($options, array('loi' => $loi));
    echo include_component('amendement', 'pagerAmendements', $options);
  }
  else { ?>
<?php if (!count($amendements)) { ?>
<p>Nous n'avons pas trouvé d'amendement correspondant à votre recherche.</p>
<?php } else { ?>
<p><?php $total = count($amendements);
  echo $total;?> amendement<?php if ($total>1) echo 's'; ?> trouvé<?php if ($total>1) echo 's'; ?>&nbsp;:</p>
<ul>
<?php foreach($amendements as $a) :?>
<li><?php 
echo link_to('Amendement n°'.$a->numero.' portant sur le texte n°'.$a->texteloi_id.', '.$a->sujet, '@amendement?loi='.$a->texteloi_id.'&numero='.$a->numero); ?></li>
<?php endforeach; ?>
</ul>
<?php } } ?>
