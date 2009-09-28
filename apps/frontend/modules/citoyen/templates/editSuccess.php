<div class="boite_form">
  <div class="b_f_h"><div class="b_f_hg"></div><div class="b_f_hd"></div></div>
    <div class="b_f_cont">
      <div class="b_f_text">
        <?php echo $form->renderFormTag(url_for('citoyen/edit')); ?>
        <table>
          <tr class="cel1">
            <th colspan="2">
              <h1>Edition de votre profil</h1>
            </th>
          </tr>
          <tr class="cel2">
            <th style="text-align:left;"><?php echo $form['activite']->renderLabel() ?></th>
            <td>
              <?php echo $form['activite']->renderError(); ?>
              <?php echo $form['activite']; ?>
            </td>
          </tr>
          <tr class="cel1">
            <th><?php echo $form['url_site']->renderLabel(); ?></th>
            <td>
              <?php echo $form['url_site']->renderError(); ?>
              <?php echo $form['url_site']; ?>
            </td>
          </tr>
          <tr class="cel2">
            <th style="text-align:left;"><?php echo $form['naissance']->renderLabel() ?></th>
            <td>
              <?php echo $form['naissance']->renderError(); ?>
              <?php echo $form['naissance']; ?>
            </td>
          </tr>
          <tr class="cel1">
            <th style="text-align:left;"><?php echo $form['sexe']->renderLabel() ?></th>
            <td>
              <?php echo $form['sexe']->renderError(); ?>
              <?php echo $form['sexe']; ?>
            </td>
          </tr>
          <tr class="cel2">
            <th>Votre Avatar</th>
            <td>
              <a href="<?php echo url_for('@upload_avatar'); ?>"><img src="<?php echo url_for('@photo_citoyen?slug='.$user->slug); ?>"/><br/>Changer d'avatar</a>
            </td>
          </tr>
          <tr class="cel1">
            <th>Votre mot de passe</th>
            <td><a href="<?php echo url_for('@editpassword_citoyen'); ?>">Changer</a></td>
          </tr>
          <tr class="cel2">
            <th colspan="2">
              <a href="<?php echo url_for('@delete_citoyen?token=' . $sf_user->getAttribute('token')) ?>" onclick="javascript:if(!confirm('Supprimer votre compte ?')) return false;">Supprimer votre compte</a>
            </th>
          </tr>
          <tr class="cel1">
            <th colspan="2"><a href="<?php echo url_for('@citoyen?slug=' . $sf_user->getAttribute('slug')) ?>">Annuler</a></th>
          </tr>
          <tr class="cel2">
            <td colspan="2"><input type="submit" value="Valider" style="float:right;" /></td>
          </tr>
        </table>
        </form>
        <br />
      </div>
    </div>
  <div class="b_f_b"><div class="b_f_bg"></div><div class="b_f_bd"></div></div>
</div>