<div class="temp">
<?php if (!count($amendements)) { ?>
<p>Nous n'avons pas trouvé d'amendmement correspondant à votre recherche.</p>
<?php }else { ?>
<p><?php echo count($amendements) ?> amendements :</p>
<ul>
<?php foreach($amendements as $a) :?>
<li><?php echo link_to('Amendement n°'.$a->numero.' portant sur "'.$a->sujet.'"', '@amendement?id='.$a->id); ?></li>
<?php endforeach; ?>
</ul>
<?php } ?></div>