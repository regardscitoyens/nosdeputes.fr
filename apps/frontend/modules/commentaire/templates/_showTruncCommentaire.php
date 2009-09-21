<?php use_helper('Text') ?>
<div id="commentaire_<?php echo $c->id; ?>" class="commentaire">
<p><span class="titre_commentaire"><a href="<?php echo url_for($c->lien); ?>#commentaire_<?php echo $c->id; ?>"><?php echo $c->getPresentation() ?></a>, <?php 
include_component('citoyen', 'shortCitoyen', array('citoyen_id'=>$c->citoyen_id));
?> a dit le <?php echo myTools::displayDate($c->created_at); ?>:</span></p>
<div class="commentaire_avatar"><?php include_component('citoyen','avatarCitoyen', array('citoyen_id'=>$c->citoyen_id)); ?></div>
<p><?php echo truncate_text(strip_tags($c->commentaire), 200); ?></p>
<p><a href="<?php echo url_for($c->lien); ?>#commentaire_<?php echo $c->id; ?>">Lire dans le contexte</a></p>
</div>
