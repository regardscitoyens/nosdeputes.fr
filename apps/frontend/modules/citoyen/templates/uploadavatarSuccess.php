<div class="temp">
<h1>Ajouter/modifier votre Avatar</h1>
<p>
<?php 
  echo $form->renderFormTag(url_for('@upload_avatar'), array('multipart=true'));
?>
  <table>
    <?php echo $form; ?>
  </table>
  <input type="submit" value="Valider" />
</p>
</form>
</div>