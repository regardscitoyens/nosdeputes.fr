<h3 id="commentaires" class="list_com"><?php echo $object->nb_commentaires.' commentaire';
  if ($object->nb_commentaires > 1) echo 's'; ?>&nbsp;:</h3>
<?php foreach($commentaires as $c) : 
if($c->is_public || $c->citoyen_id == $sf_user->getAttribute('user_id') ) :
  $options = array('c' => $c);
  if (isset($presentation)) $options = array_merge($options, array('presentation' => $presentation));
  include_partial('commentaire/showCommentaire', $options);
endif; endforeach; ?>
