<div class="boite_form">
  <div class="b_f_h"><div class="b_f_hg"></div><div class="b_f_hd"></div></div>
    <div class="b_f_cont">
      <div class="b_f_text">
        <?php echo $form->renderFormTag(url_for('@upload_avatar'), array('multipart=true')); ?>
        <table>
          <tr class="cel1">
            <th colspan="2">
              <h1>Ajouter/Modifier votre avatar</h1>
            </th>
          </tr>
          <tr class="cel2">
            <th style="text-align:left;"><?php echo $form['photo']->renderLabel() ?></th>
            <td>
              <?php echo $form['photo']->renderError(); ?>
              <?php echo $form['photo']; ?>
              <input type="submit" value="ok" />
            </td>
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