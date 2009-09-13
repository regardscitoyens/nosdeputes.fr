<div class="temp">
<h1>Edition de votre profil</h1>
<p><a href="<?php echo url_for('@upload_avatar'); ?>">Ajouter/modifier votre Avatar</a></p>
<p><a href="<?php echo url_for('@editpassword_citoyen'); ?>">Changer votre mot de passe</a></p>
<h2>Modifier vos infos</h2>
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