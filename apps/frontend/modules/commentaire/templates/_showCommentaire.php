<?php use_helper('Text') ?>
<div class="commentaire">
<p><span class="titre_commentaire"><?php 
include_component('citoyen', 'shortCitoyen', array('citoyen_id'=>$c->citoyen_id));
?> a dit le <?php echo myTools::displayDate($c->created_at); ?>:</a></span></p>
<p><?php echo $c->commentaire; ?></p>
</div>
