<div class="boite_form">
  <div class="b_f_h"><div class="b_f_hg"></div><div class="b_f_hd"></div></div>
    <div class="b_f_cont">
      <div class="b_f_text">
        <?php echo $form->renderFormTag(url_for('citoyen/editpassword')); ?>
        <table>
          <tr class="cel1">
            <th colspan="2">
              <h1>Changer votre mot de passe</h1>
            </th>
          </tr>
          <tr class="cel2">
            <th style="text-align:left;"><?php echo $form['ancienpassword']->renderLabel() ?></th>
            <td>
              <?php echo $form['ancienpassword']->renderError(); ?>
              <?php echo $form['ancienpassword']; ?>
            </td>
          </tr>
          <tr class="cel1">
            <th><?php echo $form['password']->renderLabel(); ?></th>
            <td>
              <?php echo $form['password']->renderError(); ?>
              <?php echo $form['password']; ?>
            </td>
          </tr>
          <tr class="cel2">
            <th><?php echo $form['password_bis']->renderLabel(); ?></th>
            <td>
              <?php echo $form['password_bis']->renderError(); ?>
              <?php echo $form['password_bis']; ?>
            </td>
          </tr>
          <tr class="cel1">
            <td colspan="2"><input type="submit" value="Valider" style="float:right;" /></td>
          </tr>
          <tr class="cel2">
            <th colspan="2"><a href="<?php echo url_for('@reset_mdp'); ?>">Mot de passe oublié ?</a></th>
          </tr>
          <tr class="cel1">
            <th colspan="2"><a href="<?php echo url_for('@citoyen?slug=' . $sf_user->getAttribute('slug')) ?>">Annuler</a></th>
          </tr>
        </table>
        </form>
        <br />
      </div>
    </div>
  <div class="b_f_b"><div class="b_f_bg"></div><div class="b_f_bd"></div></div>
</div>