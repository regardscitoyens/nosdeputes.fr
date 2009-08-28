<?php foreach($commentaires as $c) : ?>
<div id="commentaire_<?php echo $c->id; ?>" class="commentaire">
<p>Le <?php echo $c->getHumainDateTime(); ?>, <?php echo $c->getHumainUser(); ?> a dit :</p>
<p><?php echo $c->commentaire; ?></p>
<p><a href="#commentaire_<?php echo $c->id;?>">Permalink</a></p>
</div>
<?php endforeach; ?>