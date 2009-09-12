<?php foreach($commentaires as $c) : ?>
<div id="commentaire_<?php echo $c->id; ?>" class="commentaire">
<p>Le <?php echo myTools::displayDate($c->created_at); ?>, <?php 
include_component('citoyen', 'shortCitoyen', array('citoyen_id'=>$c->citoyen_id));
?> a dit :</p>
<p><?php 
if($c->is_public)
    echo $c->commentaire;
?></p>
<p><a href="#commentaire_<?php echo $c->id;?>">Permalink</a></p>
<p>Vous trouvez ce commentaire constructif : <?php include_component('rate', 'show', array('object' => $c)); ?> </p>
</div>
<?php endforeach; ?>
