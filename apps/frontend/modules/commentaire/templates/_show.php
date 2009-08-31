<?php foreach($commentaires as $c) : ?>
<div id="commentaire_<?php echo $c->id; ?>" class="commentaire">
<p>Le <?php echo $c->getHumanDateTime(); ?>, <?php echo $c->getHumanUser(); ?> a dit :</p>
<p><?php echo $c->commentaire; ?></p>
<p><a href="#commentaire_<?php echo $c->id;?>">Permalink</a></p>
</div>
<?php endforeach; ?>