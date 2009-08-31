<div class="temp">
<h1>Connexion</h1>
<p>
<form method="post" action="<?php echo url_for('@signin'); ?>">
  <table>
    <?php echo $form; ?>
  </table>
  <input type="submit" value="Ok" />
  <a href="#">Mot de passe oublié ?</a>
</p>
</form>
</div>