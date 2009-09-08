<?php foreach($commentaires as $c) : ?>
<div id="commentaire_<?php echo $c->id; ?>" class="commentaire">
<p>Le <?php echo myTools::displayDate($c->created_at); ?>, <?php 
include_component('citoyen', 'shortCitoyen', array('citoyen_id'=>$c->citoyen_id));
?> a dit :</p>
<p><?php echo $c->commentaire; ?></p>
<p><a href="#commentaire_<?php echo $c->id;?>">Permalink</a></p>
</div>
<?php endforeach; ?>