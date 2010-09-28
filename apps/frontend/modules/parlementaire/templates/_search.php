<?php if (count($parlementaires) || count($similars)) : ?>
<p><?php echo $msg; ?></p>
<ul>
<?php
if (count($parlementaires)) {
  foreach($parlementaires as $parlementaire) {
    echo "<li>".link_to($parlementaire->nom, '@parlementaire?slug='.$parlementaire->slug)."</li>";
  }
 }else foreach($similars as $parlementaire) {
   if ($parlementaire)
     echo "<li>".link_to($parlementaire['nom'], '@parlementaire?slug='.$parlementaire['slug'])."</li>";
       }
?></ul><?php endif; ?>