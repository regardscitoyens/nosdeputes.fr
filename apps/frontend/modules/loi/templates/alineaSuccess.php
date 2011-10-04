<div class="loi">
<h1><?php echo link_to($loi->titre, '@loi?loi='.$loi->texteloi_id); ?></h1>
<h2><?php echo link_to('Article '.$titre_article, '@loi_article?loi='.$alinea->texteloi_id.'&article='.$slug_article); ?> - Alinéa <?php echo $alinea->numero; ?></h2>
<br/>
<table class="alineas">
<?php foreach ($alineas as $a) {
  echo include_partial('alinea', array('a'=>$a, 'alinea' => $alinea, 'slug_article'=>$slug_article));
} ?>
</table>
</div>
<p class="suivant"><b><a href="<?php echo url_for('@loi_article?loi='.$alinea->texteloi_id.'&article='.$slug_article); ?>">Voir tout l'article</a></b></p>
<div class="commentaires">
<?php echo include_component('commentaire', 'showAll', array('object' => $alinea, 'type' => 'cet alinéa'));
echo include_component('commentaire', 'form', array('object' => $alinea));
?>
</div>
