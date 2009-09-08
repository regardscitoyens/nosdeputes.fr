<?php if (!isset($amendements))
  echo include_component('amendement', 'pagerAmendements', array('amendement_query' => $amendements_query, 'lois' => $lois));
  else { ?>
<div class="temp">
<?php if (!count($amendements)) { ?>
<p>Nous n'avons pas trouvé d'amendement correspondant à votre recherche.</p>
<?php } else { ?>
<p><?php echo count($amendements) ?> amendements :</p>
<ul>
<?php foreach($amendements as $a) :?>
<li><?php echo link_to('Amendement n°'.$a->numero.' portant sur "'.$a->sujet.'"', '@amendement?id='.$a->id); ?></li>
<?php endforeach; ?>
</ul>
<?php } ?>
</div>
<?php } ?>
