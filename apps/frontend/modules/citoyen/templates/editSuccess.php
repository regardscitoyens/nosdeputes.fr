<div class="temp">
<h1>Edition de votre profil</h1>
 <?php if ($sf_user->hasFlash('notice')): ?>
  <p class="notice"><?php echo $sf_user->getFlash('notice') ?></p>
<?php endif; ?>
<p>
<?php 
	echo $form->renderFormTag(url_for('citoyen/edit'));
?>
  <table>
		<tr>
			<th>Nom d'utilisateur</th>
			<td><?php #echo $user->Username; ?></td>
		</tr>
    <?php echo $form; ?>
  </table>
  <input type="submit" value="Valider" />
</p>
</form>
</div>