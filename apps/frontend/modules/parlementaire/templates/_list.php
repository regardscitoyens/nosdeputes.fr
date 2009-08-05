<?php
if (!$route)
  $route = '@parlementaire?slug=';

?>
<ul><?php foreach($parlementaires as $p) : ?>
<li><?php echo link_to($p->nom, $route.$p->slug).' '.$p->nb; ?>
<?php endforeach; ?>
</ul>