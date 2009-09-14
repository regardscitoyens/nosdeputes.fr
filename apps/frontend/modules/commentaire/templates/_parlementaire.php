<?php use_helper('Text');

if (!count($commentaires)) : ?>
<p>Le travail de ce député n'a pas encore inspiré de commentaire aux utilisateurs.</p>
<?php else : 
  foreach($commentaires as $c) {
include_partial('commentaire/showTruncCommentaire', array('c'=>$c));
}
endif;?>
