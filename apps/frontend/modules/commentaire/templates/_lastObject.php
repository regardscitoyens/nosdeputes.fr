<?php foreach($commentaires as $c) {
  $options = array('c'=>$c);
  if (isset($citoyen)) $options = array_merge($options, array('citoyen' => $citoyen));
  if (isset($presentation)) $options = array_merge($options, array('presentation' => $presentation));
 include_partial('commentaire/showTruncCommentaire', $options);
} ?>
