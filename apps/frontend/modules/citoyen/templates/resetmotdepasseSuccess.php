<div class="temp">
<h1>Mot de passe oublié</h1>
<?php 
if ($sf_user->hasAttribute('resetmdp')) { $route = 'citoyen/resetmotdepasse?slug='.$slug.'&token='.$token; } else { $route = 'citoyen/resetmotdepasse'; }
echo $form->renderFormTag(url_for($route));
?>
  <table>
    <?php echo $form; ?>
  </table>
  <input type="submit" value="Valider" />
</p>
</form>
</div>