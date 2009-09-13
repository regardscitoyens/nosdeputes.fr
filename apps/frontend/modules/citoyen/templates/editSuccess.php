<div class="temp">
<h1>Edition de votre profil</h1>
<h2>Modifier vos infos</h2>
<p>
<?php 
  echo $form->renderFormTag(url_for('citoyen/edit'));
?>
  <table>
    <?php echo $form; ?>
<tr><td>Votre Avatar</td><td><a href="<?php echo url_for('@upload_avatar'); ?>"><img src="<?php echo url_for('@photo_citoyen?slug='.$user->slug); ?>"/><br/>Changer d'avatar</a></td></tr>
<tr><td>Votre mot de passe</td><td><a href="<?php echo url_for('@editpassword_citoyen'); ?>">Le changer</a></td></tr>
  </table>
  <input type="submit" value="Valider" />
</p>
</form>
</div>