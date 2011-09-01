<?php
if (!isset($article['id']) || !$article['id'])
  return;
?><p><?php echo myTools::escape_blanks($article['corps']); ?></p>
<?php if ($sf_user->isAuthenticated() && !$sf_user->hasCredential('membre'))
  echo '<h3>'.link_to('Éditer', '@doc_organisme_edit?article_id='.$article['id']).'</h3>';
?>
