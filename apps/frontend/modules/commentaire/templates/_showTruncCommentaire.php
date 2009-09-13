<div class="commentaire">
<p><a href="<?php echo url_for($c->lien); ?>#commentaire_<?php echo $c->id; ?>"><?php echo $c->getPresentation() ?>, <?php 
include_component('citoyen', 'shortCitoyen', array('citoyen_id'=>$c->citoyen_id));
?> a dit le <?php echo myTools::displayDate($c->created_at); ?>:</a></p>
<p><?php echo truncate_text($c->commentaire, 500); ?></p>
<p><a href="<?php echo url_for($c->lien); ?>#commentaire_<?php echo $c->id; ?>">Lire dans le contexte</a></p>
</div>
