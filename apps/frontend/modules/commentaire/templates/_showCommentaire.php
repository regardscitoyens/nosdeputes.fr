<?php use_helper('Text') ?>
<div id="commentaire_<?php echo $c->id; ?>" class="commentaire">
<p><span class="titre_commentaire">Le <a href="#commentaire_<?php echo $c->id;?>"><?php echo myTools::displayDateTime($c->created_at); ?></a>, <?php
include_component('citoyen', 'shortCitoyen', array('citoyen_id'=>$c->citoyen_id));
?> a dit :</span></p>
<div class="commentaire_avatar"><?php include_component('citoyen','avatarCitoyen', array('citoyen_id'=>$c->citoyen_id)); ?></div>
<p><?php echo $c->commentaire; ?></p>
<?php
if(!$c->is_public)
  echo "<p><strong>Attention, ce commentaire est en attente de
  validation par email. Les autres utilisateurs ne peuvent pas le
  voir.</strong></p>";
?>
<p class="clear" align="right">Vous trouvez ce commentaire constructif : <?php include_component('rate', 'show', array('object' => $c)); ?> </p>
</div>
