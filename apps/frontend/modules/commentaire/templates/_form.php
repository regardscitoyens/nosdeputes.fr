<table>
<?php echo $form->renderFormTag(url_for('@commentaire_post?type='.$type.'&id='.$id)); ?>
<?php echo $form; ?>
</table>
<input type='submit' value='Prévisualiser'/>
<?php if (isset($sendButton)) : ?>
<input type='submit' name='ok' value='Envoyer'/>
<?php endif; ?>
