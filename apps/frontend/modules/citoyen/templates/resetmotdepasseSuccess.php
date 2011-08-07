<?php $sf_response->setTitle('Changement de mot de passe - NosDéputés.fr'); ?>
<?php 
if (!isset($first))
{
?>
<div class="boite_form">
  <div class="b_f_h"><div class="b_f_hg"></div><div class="b_f_hd"></div></div>
    <div class="b_f_cont">
      <div class="b_f_text">
        <?php echo $form->renderFormTag(url_for('citoyen/resetmotdepasse?slug='.$slug.'&activation_id='.$activation_id)); ?>
        <table>
          <tr>
            <th colspan="2">
              <h1>Choisissez un mot de passe</h1>
            </th>
          </tr>
          <tr>
            <th><?php echo $form['password']->renderLabel() ?></th>
            <td>
              <?php echo $form['password']->renderError(); ?>
              <?php echo $form['password']->render(array('tabindex' => '10')); ?>
            </td>
          </tr>
          <tr>
            <th><?php echo $form['password_bis']->renderLabel() ?></th>
            <td>
              <?php echo $form['password_bis']->renderError(); ?>
              <?php echo $form['password_bis']->render(array('tabindex' => '20')); ?>
            </td>
          </tr>
          <tr>  
            <td colspan="2"><a href="<?php echo url_for('@homepage') ?>" tabindex="40">Annuler</a> <input type="submit" tabindex="30" value="Valider" /></td>
          </tr>
        </table>
        </form>
        <br />
      </div>
    </div>
  <div class="b_f_b"><div class="b_f_bg"></div><div class="b_f_bd"></div></div>
</div>
<?php
}
else
{
?>
<div class="boite_form">
  <div class="b_f_h"><div class="b_f_hg"></div><div class="b_f_hd"></div></div>
    <div class="b_f_cont">
      <div class="b_f_text">
        <?php echo $form->renderFormTag(url_for('citoyen/resetmotdepasse')); ?>
        <table>
          <tr>
            <th colspan="2">
              <h1>Demande de mot de passe</h1>
            </th>
          </tr>
          <tr class="cel2">
            <th><?php echo $form['login']->renderLabel() ?></th>
            <td>
              <?php echo $form['login']->renderError(); ?>

              <?php echo $form['login']->render(array('tabindex' => '10')); ?>
            </td>
          </tr>
          <tr>
            <th colspan="2" style="text-align:center;padding-left:40px;">
              <?php echo $form['code']->renderError(); ?>
              <div class="captcha">
                <div class="image_captcha">
                  <img src="<?php echo url_for('@captcha_image'); ?>" alt="Code" id="codesecu" />
                </div>
                <div class="images_fonctions">
                  <a href="<?php echo url_for('@captcha_sonore'); ?>"><?php echo image_tag('xneth/sound.png', array('alt' => 'écouter le code de sécurité', 'title' => 'écouter le code de sécurité')); ?></a>
                  <a href="#" onclick="document.getElementById('codesecu').src='<?php echo url_for('@captcha_image'); ?>?re=' + Math.random(); return false"><?php echo image_tag('xneth/reload.png', array('alt' => 'Obtenir un nouveau code de sécurité', 'title' => 'Obtenir un nouveau code de sécurité')); ?></a>
                </div>
                <div class="cont_code">
          <div class="entrer_code">
                  Code de sécurité<br />
                  <?php echo $form['code']->render(array('size' => 8, 'title' => 'Recopiez les caractères', 'tabindex' => '20')); ?>
          </div>
                </div>
                <div class="stopfloat"></div>
              </div>
            </th>
          </tr>
          <tr class="cel2">
            <td colspan="2" style="text-align:center;"><a href="<?php echo url_for('@homepage') ?>" tabindex="40" >Annuler</a> <input type="submit" value="Valider" tabindex="30" /></td>
          </tr>
        </table>
        </form>
        <br />
      </div>
    </div>
  <div class="b_f_b"><div class="b_f_bg"></div><div class="b_f_bd"></div></div>
</div>
<?php
}
?> 
