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