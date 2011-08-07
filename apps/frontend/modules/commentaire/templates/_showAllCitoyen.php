<h2 class="list_com"><?php $comments = count($commentaires);
  if ($comments == 0) echo "Aucun commentaire";
  else echo '<span class="list_com">'.$comments." commentaire".($comments > 1 ? 's' : '').'</span>';
?></h2>
<?php include_partial('commentaire/lastObject', array('commentaires' => $commentaires)); ?>
