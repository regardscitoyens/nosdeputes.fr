<?php foreach($commentaires as $c) : 
if($c->is_public || $c->citoyen_id == $sf_user->getAttribute('user_id') ) :
?>
<div id="commentaire_<?php echo $c->id; ?>" class="commentaire">
<div class="commentaire_avatar"><?php include_component('citoyen','avatarCitoyen', array('citoyen_id'=>$c->citoyen_id)); ?></div>
<span class="titre_commentaire">Le <?php echo myTools::displayDateTime($c->created_at); ?>, <?php 
include_component('citoyen', 'shortCitoyen', array('citoyen_id'=>$c->citoyen_id));
?> a dit :</span>
<?php echo $c->commentaire; ?>
<?php
if(!$c->is_public)
  echo "<p><strong>Attention, ce commentaire est en attente de
  validation par email. Les autres utilisateurs ne peuvent pas le
  voir.</strong></p>";
?>
<p><a href="#commentaire_<?php echo $c->id;?>">Permalien</a></p>
<p>Vous trouvez ce commentaire constructif : <?php include_component('rate', 'show', array('object' => $c)); ?> </p>
</div>
<?php endif; endforeach; ?>
