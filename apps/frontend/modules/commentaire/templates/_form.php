<div id="ecrire"></div><?php echo $form->renderFormTag(url_for('@commentaire_post?type='.$type.'&id='.$id)); ?>
<table>
  <tr>
    <th colspan="3" style="text-align:left;"><?php echo $form['commentaire']->renderLabel() ?></th>
  </tr>
  <tr>
    <td colspan="3">
      <?php echo $form['commentaire']->renderError(); ?>
      <?php echo $form['commentaire']->render(array('style' => 'width:73.5em')); ?>
    </td>
  </tr>
  <?php if (!$sf_user->isAuthenticated()) { ?>
  <tr>
    <td>
      <table class="inscription">
	    <tr>
		  <th colspan="3">Inscription</th>
		</tr>
	    <tr>
		  <th><?php echo $form['nom']->renderLabel(); ?></th>
		  <td>
		  <?php echo $form['nom']->renderError(); ?>
          <?php echo $form['nom']; ?>
		  </td>
		</tr>
	    <tr>
		  <th><?php echo $form['email']->renderLabel(); ?></th>
		  <td>
		  <?php echo $form['email']->renderError(); ?>
          <?php echo $form['email']; ?>
		  </td>
		</tr>
      </table>
	</td>
	<th>ou</th>
	<td>
	  <table class="connexion">
	    <tr>
		  <th colspan="3">Connexion</th>
		</tr>
		<tr>
		  <th><?php echo $form['login']->renderLabel(); ?></th>
		  <td>
		  <?php echo $form['login']->renderError(); ?>
          <?php echo $form['login']; ?>
		  </td>
		</tr>
		<tr>
		  <th><?php echo $form['password']->renderLabel(); ?></th>
		  <td colspan="2">
		  <?php echo $form['password']->renderError(); ?>
          <?php echo $form['password']; ?>
		  </td>
		</tr>
      </table>
	</td>
  </tr>
  <?php } ?>
  <tr><td><input type="checkbox" name="follow_talk" id="follow_talk"<?php if  ($follow_talk) echo " checked"; ?>/><label for="follow_talk">M'alerter par email lorsque quelqu'un réagit à mon commentaire</label></td></tr>
  <tr>
    <td colspan="3" style="height:40px;">
      <input type="hidden" name="unique_form" value="<?php echo $unique_form; ?>"/>
      <input type='submit' value='Prévisualiser'/>
      <?php if (isset($sendButton)) : ?>
      <input type='submit' name='ok' value='Envoyer'/>
      <?php endif; ?>
    </td>
  </tr>
</table>
</form>
