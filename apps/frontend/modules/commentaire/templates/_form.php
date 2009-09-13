<?php echo $form->renderFormTag(url_for('@commentaire_post?type='.$type.'&id='.$id)); ?>
<table>
  <tr>
    <th style="text-align:left;"><?php echo $form['commentaire']->renderLabel() ?></th>
  </tr>
  <tr>
    <td>
      <?php echo $form['commentaire']->renderError(); ?>
      <?php echo $form['commentaire']->render(array('cols' => '100%')); ?>
    </td>
  </tr>
  <tr>
  <td>
<?php if (!$sf_user->isAuthenticated()) { ?>
<div class="cont_multiform">
<div class="multiform">
  <div class="tab_form" id="tab_form_1">
    <h4>Inscription</h4>
    <table>
    <tr>
    <th>
    <?php echo $form['nom']->renderLabel(); ?></th>
    <td>
    <?php echo $form['nom']->renderError(); ?>
    <?php echo $form['nom']; ?>
    </td>
    </tr>
    <tr>
    <th>
    <?php echo $form['email']->renderLabel(); ?></th>
    <td>
    <?php echo $form['email']->renderError(); ?>
    <?php echo $form['email']; ?>
    </td>
    </tr>
    </table>
  </div>
  <div class="tab_form" id="tab_form_2">
    <h4>Connexion</h4>
    <table>
    <tr>
    <th>
    <?php echo $form['login']->renderLabel(); ?></th>
    <td>
    <?php echo $form['login']->renderError(); ?>
    <?php echo $form['login']; ?>
    </td>
    </tr>
    <tr>
    <th>
    <?php echo $form['password']->renderLabel(); ?></th>
    <td>
    <?php echo $form['password']->renderError(); ?>
    <?php echo $form['password']; ?>
    </td>
    </tr>
    </table>
  </div>
</div>
</div>
<?php } ?>
  </td>
  </tr>
  <tr>
    <td colspan="2">
      <input type="hidden" name="unique_form" value="<?php echo $unique_form; ?>"/>
      <input type='submit' value='Prévisualiser'/>
      <?php if (isset($sendButton)) : ?>
      <input type='submit' name='ok' value='Envoyer'/>
      <?php endif; ?>
    </td>
  </tr>
</table>
</form>
