<div class="boite_form">
  <div class="b_f_h"><div class="b_f_hg"></div><div class="b_f_hd"></div></div>
    <div class="b_f_cont">
      <div class="b_f_text">
        <?php echo $form->renderFormTag(url_for('citoyen/signin')); ?>
        <table>
          <tr>
            <th colspan="2">
              <h1>Connexion</h1>
            </th>
          </tr>
          <tr>
            <th style="text-align:left;"><?php echo $form['login']->renderLabel() ?></th>
            <td>
              <?php echo $form['login']->renderError(); ?>
              <?php echo $form['login']; ?>
            </td>
          </tr>
          <tr>
            <th><?php echo $form['password']->renderLabel(); ?></th>
            <td>
              <?php echo $form['password']->renderError(); ?>
              <?php echo $form['password']; ?>
            </td>
          </tr>
          <tr>
            <th><?php echo $form['remember']->renderLabel(); ?></th>
            <td>
              <?php echo $form['remember']->renderError(); ?>
              <?php echo $form['remember']; ?>
            </td>
          </tr>
          <tr>
            <th></th>
            <td><input type="submit" value="ok" style="float:right;" /></td>
          </tr>
          <tr>
            <th colspan="2">
              <a href="<?php echo url_for('@reset_mdp') ?>">Vous avez oublié votre mot de passe ?</a><br />
              <a href="<?php echo url_for('@inscription') ?>">Vous inscrire</a>
            </th>
          </tr>
        </table>
        </form>
        <br />
      </div>
    </div>
  <div class="b_f_b"><div class="b_f_bg"></div><div class="b_f_bd"></div></div>
</div> 