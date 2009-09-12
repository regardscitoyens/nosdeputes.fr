<?php 
if (isset($titre)) echo '<h1>'.$titre.'</h1>';
?>
<div class="preview">
   <h2><?php echo $form->getValue('titre'); ?></h2>
   <div><?php echo $article; ?></div>
</div>
<form method="post">
<table>
<?php echo $form; ?>
</table>
<input type="submit" value="Visualiser">
<?php if (isset($post) && $post) { ?>
<input type="submit" name="ok" value="<?php if ($form->getObject()->id) {echo 'Mettre à jour'; } else echo 'Créer'; ?>">
<?php } 
if ($form->getObject()->id) 
  echo link_to('Supprimer', '@article_delete?article_id='.$form->getObject()->id); ?>
</form>