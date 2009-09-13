<?php foreach($commentaires as $c) {
?>
<div id="commentaire_<?php echo $c->id; ?>" class="commentaire">
<p>Le <?php echo myTools::displayDateTime($c->created_at); ?>, <?php 
include_component('citoyen', 'shortCitoyen', array('citoyen_id'=>$c->citoyen_id));
?> a dit :</p>
<p><?php echo $c->commentaire; ?></p>
<p><?php echo link_to('Voir le commentaire dans son contexte', $c->lien.'#commentaire_'.$c->id); ?></p>
</div>
<?php } ?>
