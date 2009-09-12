<div id='com_ajax_<?php echo $id; ?>' style="display: none"><?php if (count($comments)) { ?>
<div><b>Les derniers Commentaires</b></div>
<?php foreach($comments as $c) 
  include_partial('showCommentaire', array('c'=>$c));
   echo link_to('Lire tous les commenaires', '@intervention?id='.$id.'#lire_commentaire');
 }
?>
<div><b>Ecrire un commentaire</b></div><?php
include_component('commentaire', 'form', array('type'=>'Intervention', 'id'=>$id));
?></div><?php
exit;