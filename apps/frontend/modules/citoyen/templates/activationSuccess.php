<div class="temp">
<h1>Finaliser votre inscription</h1>
<?php echo $form->renderFormTag(url_for('citoyen/activation?activation_id='.$activation_id)) ?>
  <table>
    <?php echo $form ?>
  </table>
 
  <input type="submit" name="register" value="Valider" />
</form>
</div>