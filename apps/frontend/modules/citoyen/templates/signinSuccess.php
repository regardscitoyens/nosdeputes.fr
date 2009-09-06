<div class="temp">
<h1>Connexion</h1>
<p>
<?php 
  echo $form->renderFormTag(url_for('citoyen/signin'));
?>
  <table>
    <?php echo $form; ?>
  </table>
  <input type="submit" value="Valider" />
</p>
</form>
<p><a href="#">mot de passe oublié ?</a></p>
</div>