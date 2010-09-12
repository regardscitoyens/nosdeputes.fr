<div class="com_ajax" id='com_ajax_<?php echo $id; ?>' style="display: none"><?php if ($ct = count($comments)) { ?>
<div><h4 class="list_com"><?php if (isset($limit)) echo 'Les derniers commentaires</span>';
  else {
    echo $ct.' commentaire';
    if ($ct > 1) echo 's';
  } ?></h4></div>
<?php foreach($comments as $c) 
    include_partial('showCommentaire', array('c'=>$c));
  if (isset($limit)) {
    if ($type == 'Intervention') $link = '@intervention?id=';
    else if ($type == 'Alinea') $link = '@loi_alinea?id=';
    echo '<div class="suivant">'.link_to('Voir tous les commentaires', $link.$id.'#lire_commentaire').'</div>';
  }
}
echo include_component('commentaire', 'form', array('type' => $type, 'id' => $id)); ?>
</div>
