<div class="temp">
<h1>Inscription</h1>
<p>Votre email : <?php echo $user->email; ?></p>
<p>Pour terminer votre inscription, veuillez choisir votre nom d'utilisateur ainsi que votre mot de passe :</p>
<?php echo $form->renderFormTag(url_for('@activation_citoyen_mail?activation_id='.$user->activation_id)) ?>
  <table>
    <?php echo $form ?>
  </table>
 
  <input type="submit" name="register" value="Valider" />
</form>
<p>Les champs munis de * sont obligatoires</p>
</div>