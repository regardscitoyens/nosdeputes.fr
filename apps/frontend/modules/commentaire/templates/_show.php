<?php foreach($commentaires as $c) : 
if($c->is_public || $c->citoyen_id == $sf_user->getAttribute('user_id') ) :
?>
<div id="commentaire_<?php echo $c->id; ?>" class="commentaire">
<p>Le <?php echo myTools::displayDate($c->created_at); ?>, <?php 
include_component('citoyen', 'shortCitoyen', array('citoyen_id'=>$c->citoyen_id));
?> a dit :</p>
<p><?php echo $c->commentaire; ?></p>
<?php
if(!$c->is_public)
	echo "<p><strong>Attention, ce commentaire est en attente de
	validation par email. Les autres utilisateurs ne peuvent pas le
	voir.</strong></p>";
?>
<p><a href="#commentaire_<?php echo $c->id;?>">Permalink</a></p>
<p>Vous trouvez ce commentaire constructif : <?php include_component('rate', 'show', array('object' => $c)); ?> </p>
</div>
<?php endif; endforeach; ?>
