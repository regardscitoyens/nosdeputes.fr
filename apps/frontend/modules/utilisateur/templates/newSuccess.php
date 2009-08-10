<div class="temp">
<h1>Nouvel Utilisateur</h1>  
<?php if ($sf_user->hasFlash('notice')): ?>
  <p class="notice"><?php echo $sf_user->getFlash('notice') ?></p>
<?php endif; ?>
 
<form action="<?php echo url_for('utilisateur/new') ?>" method="post">
  <table>
    <?php echo $form ?>
    <tr>
      <td></td>
      <td><input type="submit" /></td>
    </tr>
  </table>
</form>
</div>
