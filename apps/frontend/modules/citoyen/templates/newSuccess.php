<div class="temp">
<h1>Inscription</h1>
 <?php if ($sf_user->hasFlash('notice')): ?>
  <p class="notice"><?php echo $sf_user->getFlash('notice') ?></p>
<?php endif; ?>
<?php echo $form->renderFormTag(url_for('citoyen/new')) ?>
  <table>
    <?php echo $form ?>
  </table>
 
  <input type="submit" name="register" value="Valider" />
</form>
</div>