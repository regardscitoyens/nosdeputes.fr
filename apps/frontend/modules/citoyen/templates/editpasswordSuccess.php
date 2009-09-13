<div class="temp">
<h1>Changer votre mot de passe</h1>
<?php 
  echo $form->renderFormTag(url_for('citoyen/editpassword'));  
?>
  <table>
    <?php echo $form; ?>
  </table>
  <input type="submit" value="Valider" />
</p>
</form>
</div>