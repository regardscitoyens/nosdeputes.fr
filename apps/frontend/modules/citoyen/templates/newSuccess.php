<div class="temp">
<h1>Inscription</h1>
<?php echo $form->renderFormTag(url_for('citoyen/new')) ?>
  <table>
    <?php echo $form ?>
  </table>
 
  <input type="submit" name="register" value="Valider" />
</form>
<p>Les champs munis de * sont obligatoires</p>
</div>