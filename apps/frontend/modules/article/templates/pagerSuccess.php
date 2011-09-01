<?php use_helper('Text') ?>
<h1><?php echo $titre; ?></h1>
<p>A venir prochainement...</p>
<?php foreach ($pager->getResults() as $article) : ?>
<div class="article">
    <h3><?php echo link_to($article->getTitre(), $article->link); ?></h3>
   <p>par <?php include_component('citoyen', 'shortCitoyen', array('citoyen_id'=>$article->citoyen_id));
 ?></p>
 <p><?php echo myTools::escape_blanks(truncate_text(strip_tags($article->corps), 500, '...')); ?></p>
 <p><?php echo link_to('Lire la suite', $article->link); ?></p>
</div>
<?php endforeach; ?>
