<?php $ct = count($commentaires);
if (!$ct)
echo '<h3 id="commentaires" class="list_com">Aucun commentaire n\'a encore été formulé sur '.$type.'.</h3>';
else { ?>
<h3 id="commentaires" class="list_com"><?php echo $ct.' commentaire';
  if ($ct > 1) echo 's'; ?>&nbsp;:</h3>
<?php foreach($commentaires as $c) : 
if($c->is_public || $c->citoyen_id == $sf_user->getAttribute('user_id') ) :
  $options = array('c' => $c);
  if (isset($presentation)) $options = array_merge($options, array('presentation' => $presentation));
  include_partial('commentaire/showCommentaire', $options);
endif; endforeach; 
} ?>
