<div class="temp">
<h1>Edition de votre profil</h1>
<p>
<?php 
  echo $form->renderFormTag(url_for('citoyen/edit'));
?>
  <table>
    <?php echo $form; ?>
  </table>
  <input type="submit" value="Valider" />
</p>
</form>
</div>