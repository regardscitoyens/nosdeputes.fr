<?php use_helper('Text') ?>
<h1><?php echo $titre; ?></h1>
<?php foreach ($pager->getResults() as $article) : ?>
<div class="article">
   <h3><?php echo $article->getTitre(); ?></h3>
   <p>par <?php include_component('citoyen', 'shortCitoyen', array('citoyen_id'=>$article->citoyen_id));
 ?></p>
 <p><?php echo truncate_text(strip_tags($article->corps), 500, 'Lire la suite'); ?></p>
   <p>Lire la suite</p>
</div>
<?php endforeach; ?>