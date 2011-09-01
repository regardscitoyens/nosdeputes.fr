<?php use_helper('Text') ?>
<div id="commentaire_<?php echo $c->id; ?>" class="commentaire_widget">
<p><span class="titre_commentaire"><a href="<?php echo url_for($c->lien); ?>#commentaire_<?php echo $c->id; ?>"><?php if (isset($presentation)) echo $c->getPresentation($presentation, 1); else echo $c->getPresentation().', ';  
include_component('citoyen', 'shortCitoyen', array('citoyen_id'=>$c->citoyen_id, 'nolink' => 1));
?> a dit le <?php echo date('d/m/Y', strtotime($c->created_at)); ?>&nbsp;:</a></span></p>
<div class="commentaire_avatar"><?php include_component('citoyen','avatarCitoyen', array('citoyen_id'=>$c->citoyen_id)); ?></div>
<p><?php echo myTools::escape_blanks(truncate_text(strip_tags($c->commentaire), 280)); ?></p>
<p><a href="<?php echo url_for($c->lien); ?>#commentaire_<?php echo $c->id; ?>">Lire dans le contexte</a></p>
</div>
