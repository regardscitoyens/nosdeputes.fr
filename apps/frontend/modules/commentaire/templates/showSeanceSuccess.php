<div id='com_ajax_<?php echo $id; ?>' style="display: none"><?php if (count($comments)) { ?>
<div><b>Les derniers Commentaires</b></div>
<?php foreach($comments as $c) 
  include_partial('showTruncCommentaire', array('c'=>$c));
   echo link_to('Lire tous les commentaires', '@intervention?id='.$id.'#lire_commentaire');
 }
?>
<?php
include_component('commentaire', 'form', array('type'=>'Intervention', 'id'=>$id));
?></div>
