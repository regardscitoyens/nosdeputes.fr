<?php foreach($commentaires as $c) : 
if($c->is_public || $c->citoyen_id == $sf_user->getAttribute('user_id') ) :
  include_partial('commentaire/showCommentaire', array('c' => $c));
endif; endforeach;
