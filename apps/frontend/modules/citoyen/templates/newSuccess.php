<div class="boite_form">
  <div class="b_f_h"><div class="b_f_hg"></div><div class="b_f_hd"></div></div>
    <div class="b_f_cont">
      <div class="b_f_text">
        <?php echo $form->renderFormTag(url_for('citoyen/new')) ?>
        <table>
          <tr class="cel1">
            <th colspan="2">
              <h1>Inscription</h1>
            </th>
          </tr>
          <tr class="cel2">
            <th style="text-align:left;"><?php echo $form['login']->renderLabel() ?></th>
            <td>
              <?php echo $form['login']->renderError(); ?>
              <?php echo $form['login']; ?>
            </td>
          </tr>
          <tr class="cel1">
            <th><?php echo $form['email']->renderLabel(); ?></th>
            <td>
              <?php echo $form['email']->renderError(); ?>
              <?php echo $form['email']; ?>
            </td>
          </tr>
          <tr class="cel2">
            <th></th>
            <td><input type="submit" value="Valider" style="float:right;" /></td>
          </tr>
        </table>
        </form>
        <br />
      </div>
    </div>
  <div class="b_f_b"><div class="b_f_bg"></div><div class="b_f_bd"></div></div>
</div>