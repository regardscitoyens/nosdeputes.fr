<div class="loi">
<h1><?php echo link_to('Article '.$n_article, '@loi_article_id?id='.$article_id); ?> - Alinéa <?php echo $alinea->numero; ?></h1>
<br/>
<table>
<?php foreach ($alineas as $a) {
  echo '<tr class="alinea';
  if ($a->numero == $alinea->numero) echo '_select';
  echo '" id="alinea_'.$a->texteloi_id.'-'.$n_article.'-'.$a->numero.'"><td class="alineanumero">'.$a->numero.'.</td><td class="alineatexte">'.$a->texte.'</td></tr>';
} ?>
</table>
<p class="suivant"><b><a href="<?php echo url_for('@loi_article_id?id='.$article_id); ?>">Voir tout l'article</a></b></p>
</div>
<div class="commentaires" id="commentaires">
<?php if ($alinea->nb_commentaires == 0)
  echo '<h3>Aucun commentaire n\'a encore été formulé sur cet alinéa</h3>';
else echo include_component('commentaire', 'showAll', array('object' => $alinea));
echo include_component('commentaire', 'form', array('object' => $alinea));
?>
</div>
