<div class="preview">
   <h2><?php echo $form->getValue('titre'); ?></h2>
   <div><?php echo $article; ?></div>
</div>
<form method="post">
<table>
<?php echo $form; ?>
</table>
<input type="submit" value="Visualiser">
<input type="submit" name="ok" value="créer">
</form>