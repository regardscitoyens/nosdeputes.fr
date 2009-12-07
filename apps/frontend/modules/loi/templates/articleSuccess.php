<div class="loi">
<h1><?php echo link_to($loi->titre, '@loi?loi='.$loi->texteloi_id); ?></h1>
<h2><?php if (preg_match('/@loi_chapitre/', $titre)) {
  $url = url_for('@loi_chapitre?loi='.$loi->texteloi_id.'&chapitre='.$section->chapitre);
  $titre = preg_replace('/@loi_chapitre/', $url, $titre);
  if (preg_match('/@loi_section/', $titre)) {
    $url = url_for('@loi_section?loi='.$loi->texteloi_id.'&chapitre='.$section->chapitre.'&section='.$section->section);
    $titre = preg_replace('/@loi_section/', $url, $titre);
  }
}
echo $titre; ?></h2>
<div class="pagerloi">
<?php if ($article->precedent) {
  echo '<div class="precedent">'.link_to('Article précédent', '@loi_article?loi='.$loi->texteloi_id.'&article='.$article->precedent).'</div>';
}
if ($article->suivant) {
    echo '<div class="suivant">'.link_to('Article suivant', '@loi_article?loi='.$loi->texteloi_id.'&article='.$article->suivant).'</div>';
  } ?>
</div>
<br/>
<?php if (isset($expose)) echo $expose.'<div class="suivant"><a href="#commentaires">Commenter</a></div>'; ?> 
<br/>
<table>
<?php foreach ($alineas as $a) {
  echo '<tr class="alinea" id="alinea_'.$a->texteloi_id.'-'.$article->slug.'-'.$a->numero.'"><td class="alineanumero"><p>'.$a->numero.'.</p></td><td class="alineatexte">'.$a->texte; ?>
  <div class="commentaires" id='com_<?php echo $a->id; ?>'><span class="com_link" id="com_link_<?php echo $a->id; ?>"><a href="<?php echo url_for('@loi_alinea?id='.$a->id); ?>#ecrire">Voir tous les commentaires - Laisser un commentaire</a></span></div></td></tr>
<?php } ?>
</table>
</div>
<div class="commentaires" id="commentaires">
<h3><?php if ($article->nb_commentaires == 0)
  echo 'Aucun commentaire n\'a encore été formulé sur cet article</h3>';
else echo include_component('commentaire', 'showAll', array('object' => $article, 'presentation' => 'noarticle'));
echo include_component('commentaire', 'form', array('object' => $article));
?>
</div>

<script>
nbCommentairesCB = function(html){
          ids = eval('(' +html+')');
          $('.com_link a').text("Laisser un commentaire");
          for(i in ids) {
            if (i < 0)
              continue;
            if (ids[i] == 1) {
              $('#com_link_'+i+' a').text("Voir le commentaire - Laisser un commentaire");
            }else {
              $('#com_link_'+i+' a').text("Voir les "+ids[i]+" commentaires - Laisser un commentaire");
            }
          }};
additional_load = function() {
    $.ajax({
      url: "<?php echo url_for('@loi_article_commentaires_json?article='.$article->id); ?>",
      success: nbCommentairesCB,
      error: nbCommentairesCB
      });
    $("table .commentaires a").bind('click', function() {
        var c = $(this).parent().parent();
        c.html('<p class="loading"> &nbsp; </p>');
        id = c.attr('id').replace('com_', '');
        showcommentaire = function(html) {
            c.html(html);
            setTimeout(function() {$('#com_ajax_'+id).slideDown("slow")}, 100);
          };
        commentaireUrl = "<?php echo url_for('@loi_alinea_commentaires?id=XXX'); ?>".replace('XXX', id);
        $.ajax({url: commentaireUrl,
               success: showcommentaire,
               error: showcommentaire});
        return false;
      });
  };
</script>
