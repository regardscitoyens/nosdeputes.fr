<div class="loi">
<h1><?php echo link_to('Article '.$titre_article, '@loi_article?loi='.$alinea->texteloi_id.'&article='.$slug_article); ?> - Alinéa <?php echo $alinea->numero; ?></h1>
<br/>
<table class="alineas">
<?php foreach ($alineas as $a) {
  echo include_partial('alinea', array('a'=>$a, 'alinea' => $alinea, 'slug_article'=>$slug_article));
} ?>
</table>
</div>
<p class="suivant"><b><a href="<?php echo url_for('@loi_article?loi='.$alinea->texteloi_id.'&article='.$slug_article); ?>">Voir tout l'article</a></b></p>
<div class="commentaires" id="commentaires">
<?php if ($alinea->nb_commentaires == 0)
  echo '<h3>Aucun commentaire n\'a encore été formulé sur cet alinéa</h3>';
else echo include_component('commentaire', 'showAll', array('object' => $alinea));
echo include_component('commentaire', 'form', array('object' => $alinea));
?>
</div>
