<ul><?php foreach($parlementaires as $p) : ?>
<li><?php echo link_to($p->nom, '@parlementaire?slug='.$p->slug).' '.$p->nb; ?>
<?php endforeach; ?>
</ul>