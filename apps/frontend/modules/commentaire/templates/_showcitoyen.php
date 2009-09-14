<?php foreach($commentaires as $c) {
?>
  <?php include_partial('commentaire/showTruncCommentaire', array('c'=>$c)); ?>
<?php } ?>
