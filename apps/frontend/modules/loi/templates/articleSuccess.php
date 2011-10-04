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
<?php $arttitre = strtolower($article->titre);
  if (isset($amendements['avant '.$arttitre])) {
  echo '<p><b>Amendement';
  if (count($amendements['avant '.$arttitre]) > 1) echo 's';
  echo ' proposant un article additionel avant l\'article '.$article->titre.'&nbsp;:</b> ';
  foreach ($amendements['avant '.$arttitre] as $adt) echo link_to('n°&nbsp;'.$adt, '@amendement?loi='.$loi->texteloi_id.'&numero='.preg_replace('/^([A-Z]{1,3})?(\d+)\s+.*$/', '\1\2', $adt)).' ';
  echo '</p>';
}
if (isset($expose)) echo myTools::escape_blanks($expose).'<div class="suivant list_com"><a href="#commentaires">Commenter</a></div>'; ?>
<br/>
<table>
<?php foreach ($alineas as $a) {
  $options = array('a'=>$a, 'slug_article'=>$article->slug, 'comment' => 1);
  $al = $arttitre.'-'.$a->numero;
  if (isset($amendements[$al])) $options = array_merge($options, array('amendements' => $amendements[$al], 'loi' => $loi->texteloi_id));
  include_partial('alinea', $options);
 } ?>
</table>
<?php if (isset($amendements[$arttitre])) {
  echo '<p><b>';
  $ct = count($amendements[$arttitre]);
  if ($ct > 1) echo 'Tous les a';
  else echo 'A';
  echo 'mendement';
  if ($ct > 1) echo 's';
  echo ' déposé';
  if ($ct > 1) echo 's';
  echo ' sur cet article&nbsp;:</b> ';
  foreach ($amendements[$arttitre] as $adt) echo link_to('n°&nbsp;'.$adt, '@amendement?loi='.$loi->texteloi_id.'&numero='.preg_replace('/^([A-Z]{1,3})?(\d+)\s+.*$/', '\1\2', $adt)).' ';
  echo '</p>';
}
if (isset($amendements['après '.$arttitre])) {
  echo '<p><b>Amendement';
  if (count($amendements['après '.$arttitre]) > 1) echo 's';
  echo ' proposant un article additionel après l\'article '.$article->titre.'&nbsp;:</b> ';
  foreach ($amendements['après '.$arttitre] as $adt) echo link_to('n°&nbsp;'.$adt, '@amendement?loi='.$loi->texteloi_id.'&numero='.preg_replace('/^([A-Z]{1,3})?(\d+)\s+.*$/', '\1\2', $adt)).' ';
  echo '</p>';
} ?>
</div>
<div class="commentaires">
<?php echo include_component('commentaire', 'showAll', array('object' => $article, 'presentation' => 'noarticle', 'type' => 'cet article'));
echo include_component('commentaire', 'form', array('object' => $article));
?>
</div>

<script type="text/javascript">
function link_n_count_it() {
  $.ajax({
  url: "<?php echo url_for('@loi_article_commentaires_json?article='.$article->id); ?>",
  success: nbCommentairesCB,
  error: nbCommentairesCB
  });
}
function fetch_reload(linkId) {
$('#'+linkId+' a').click();
};
function highlight_coms(linkIdNum, nbComs) {
  var offset_alinea = $('#com_link_'+linkIdNum+' a').parent().parent().parent().parent().offset();
  $('body').after('<div class="coms" style="position:absolute; top:'+(Math.round(offset_alinea.top)-1)+'px; left:'+(Math.round(offset_alinea.left)-35)+'px;"><a href="javascript:fetch_reload(\'com_link_'+linkIdNum+'\')">'+nbComs+'</a></div>');
}
nbCommentairesCB = function(html){
  ids = eval('(' +html+')');
  $('.com_link a').text("Laisser un commentaire");
  for(i in ids) {
    if (i < 0)
      continue;
    if (ids[i] == 1) {
      $('#com_link_'+i+' a').text("Voir le commentaire - Laisser un commentaire");
      highlight_coms(i, ids[i]);
    }else {
      $('#com_link_'+i+' a').text("Voir les "+ids[i]+" commentaires - Laisser un commentaire");
	  highlight_coms(i, ids[i]);
    }
  }
};
additional_load = function() {
  link_n_count_it();
  $("table .commentaires a").bind('click', function() {
    $('.coms').remove();
    var c = $(this).parent().parent();
    c.html('<p class="loading"> &nbsp; </p>');
    id = c.attr('id').replace('com_', '');
    showcommentaire = function(html) {
      c.html(html);
      setTimeout(function() {$('#com_ajax_'+id).slideDown("slow", function() {
      link_n_count_it();})}, 100);
    };
    commentaireUrl = "<?php echo url_for('@loi_alinea_commentaires?id=XXX'); ?>".replace('XXX', id);
    $.ajax({url: commentaireUrl,
    success: showcommentaire ,
    error: showcommentaire });
    return false;
    });
  $(window).resize(function() {
	$('.coms').remove();
    link_n_count_it();
  });
};
</script>
