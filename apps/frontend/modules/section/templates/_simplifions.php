<div>
<h2>Les dossiers en discussion sur NosDeputes.fr</h2>
<ul>
<?php foreach($lois as $l) : ?>
<li><?php echo link_to($l->titre, "@loi?loi=".$l->texteloi_id); ?> (<?php echo $l->nb_commentaires; ?> commentaires)</li>
<?php endforeach; ?>
</ul>
</div>
