<div class="boite_citoyen">
  <div class="b_c_h"><div class="b_c_hg"></div><div class="b_c_hd"></div></div>
    <div class="b_c_cont">
      <div class="b_c_photo">  
        <?php echo '<img src="'.url_for('@photo_citoyen?slug='.$user->slug).'" alt="Photo de '.$user->login.'"/>'; ?>
      </div>
      <div class="b_c_text">
        <h1 style="text-align:left;"><?php echo $user->login; ?></h1>
        <ul>
<?php if (!empty($user->activite)) { $activite = $user->activite; } else { $activite = 'non renseigné'; }
  echo '<li>Activité : '.$activite.'</li>';
  echo '<li>Statut : '.ucfirst($user->role).'</li>';
  echo '<li>Inscrit depuis le '.myTools::displayDate($user->created_at).'</li>';
if (!empty($user->url_site)) { echo '<li><a href="'.$user->url_site.'" rel="nofollow">Site web</a></li>'; }
?>
        </ul>
      </div>
    </div>
  <div class="b_c_b"><div class="b_c_bg"></div><div class="b_c_bd"></div></div>
</div>
<div class="stopfloat"></div>
<div class="form">
  <?php echo $form->renderFormTag(url_for('citoyen/editpassword')); ?>
  <table>
    <tr class="cel1">
      <th colspan="2">
        <h2>Changer votre mot de passe</h2>
      </th>
    </tr>
    <tr class="cel2">
      <th style="text-align:left;"><?php echo $form['ancienpassword']->renderLabel() ?></th>
      <td>
        <?php echo $form['ancienpassword']->renderError(); ?>
        <?php echo $form['ancienpassword']->render(array('tabindex' => '10')); ?>
      </td>
    </tr>
    <tr class="cel1">
      <th><?php echo $form['password']->renderLabel(); ?></th>
      <td>
        <?php echo $form['password']->renderError(); ?>
        <?php echo $form['password']->render(array('tabindex' => '20')); ?>
      </td>
    </tr>
    <tr class="cel2">
      <th><?php echo $form['password_bis']->renderLabel(); ?></th>
      <td>
        <?php echo $form['password_bis']->renderError(); ?>
        <?php echo $form['password_bis']->render(array('tabindex' => '30')); ?>
      </td>
    </tr>
    <tr class="cel1">
      <td colspan="2"><a href="<?php echo url_for('@edit_citoyen') ?>" tabindex="50" >Annuler</a> <input type="submit" value="Valider" tabindex="40" /></td>
    </tr>
    <tr class="cel2">
      <th colspan="2"><a href="<?php echo url_for('@reset_mdp'); ?>" tabindex="60" onclick="javascript:if(!confirm('Recevoir un email de réinitialisation de mot de passe ?')) return false;">Mot de passe oublié ?</a></th>
    </tr>
  </table>
  </form>
  <br />
</div>