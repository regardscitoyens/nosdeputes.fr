<?php echo $form->renderFormTag(url_for('@commentaire_post?type='.$type.'&id='.$id)); ?>
<table>
<?php echo $form; ?>
</table>
<input type="hidden" name="unique_form" value="<?php echo $unique_form; ?>"/>
<input type='submit' value='Prévisualiser'/>
<?php if (isset($sendButton)) : ?>
<input type='submit' name='ok' value='Envoyer'/>
<?php endif; ?>
</form>
