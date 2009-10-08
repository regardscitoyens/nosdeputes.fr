<?php 
if (!$first)
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
              <h1>Choisissez un nouveau mot de passe</h1>
            </th>
          </tr>
          <tr>
            <th><?php echo $form['password']->renderLabel() ?></th>
            <td>
              <?php echo $form['password']->renderError(); ?>
              <?php echo $form['password']; ?>
            </td>
          </tr>
          <tr>
            <th><?php echo $form['password_bis']->renderLabel() ?></th>
            <td>
              <?php echo $form['password_bis']->renderError(); ?>
              <?php echo $form['password_bis']; ?>
            </td>
          </tr>
          <tr>  
            <td colspan="2"><a href="<?php echo url_for('@homepage') ?>">Annuler</a> <input type="submit" value="Valider" style="float:right;" /></td>
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
              <h1>Mot de passe oublié</h1>
            </th>
          </tr>
          <tr>
            <th><?php echo $form['login']->renderLabel() ?></th>
            <td>
              <?php echo $form['login']->renderError(); ?>
              <?php echo $form['login']; ?>
            </td>
          </tr>
          <tr>
            <th colspan="2">
							<div class="captcha">
							  <div class="image_captcha">
									<img src="<?php echo url_for('@captcha'); ?>" alt="Code" id="code" style="border: 1px solid #E8E7E4;" />
								</div>
								<div class="images_fonctions">
									<a href="<?php echo url_for('@captcha_sonore'); ?>"><?php echo image_tag('xneth/sound.png', array('alt' => 'écouter le captcha', 'title' => 'écouter le captcha')); ?></a>
									<a href="#" onclick="document.getElementById('code').src='<?php echo url_for('@captcha'); ?>?re=' + Math.random(); return false"><?php echo image_tag('xneth/reload.png', array('alt' => 'Obtenir une nouvelle image', 'title' => 'Obtenir une nouvelle image')); ?></a>
								</div>
								<div class="entrer_code">
									Code de sécurité : <br />
									<input type="text" />
								</div>
								<div class="stopfloat"></div>
							</div>
						</th>
          </tr>
          <tr>
            <td colspan="2"><input type="submit" value="Valider" style="float:right;" /></td>
          </tr>
          <tr>
            <th colspan="2"><a href="<?php echo url_for('@homepage') ?>">Annuler</a></th>
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