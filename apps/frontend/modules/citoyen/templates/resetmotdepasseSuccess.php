<div class="temp">
<?php 
if ($sf_user->hasAttribute('resetmdp'))
{
  $route = 'citoyen/resetmotdepasse?slug='.$slug.'&token='.$token;
	$titre = 'Choisissez un nouveau mot de passe';
}
else
{
  $route = 'citoyen/resetmotdepasse';
	$titre = 'Mot de passe oublié';
}

echo '<h1>'.$titre.'</h1>';
echo $form->renderFormTag(url_for($route));
?>
  <table>
    <?php echo $form; ?>
  </table>
  <input type="submit" value="Valider" />
</p>
</form>
</div>