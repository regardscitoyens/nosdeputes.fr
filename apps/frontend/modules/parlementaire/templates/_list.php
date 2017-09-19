<?php
if (!$route)
  $route = '@parlementaire?slug=';

?>
<ul><?php foreach($parlementaires as $inter) : ?>
<li><?php echo link_to($inter['Parlementaire']['nom'].($inter['fonction'] ? ", <i>".$inter['fonction']."</i>" : ""), $route.$inter['Parlementaire']['slug']).' (<span class="list_inter">'.$inter['nb']." mot"; if ($inter['nb'] > 1) echo "s"; ?></span>)
<?php endforeach; ?>
</ul>
