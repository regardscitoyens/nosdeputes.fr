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
        <form method="post" action="<?php echo url_for('citoyen/edit'); ?>">
        <table>
          <tr class="cel1">
            <th colspan="2">
              <h2>Edition de votre profil</h2>
              <input type="hidden" name="sf_method" value="put" />
            </th>
          </tr>
          <tr class="cel2">
            <th style="text-align:left;"><?php echo $form['activite']->renderLabel() ?></th>
            <td>
              <?php echo $form['activite']->renderError(); ?>
              <?php echo $form['activite']->render(array('tabindex' => '10')); ?>
            </td>
          </tr>
          <tr class="cel1">
            <th><?php echo $form['url_site']->renderLabel(); ?></th>
            <td>
              <?php echo $form['url_site']->renderError(); ?>
              <?php echo $form['url_site']->render(array('tabindex' => '20')); ?>
            </td>
          </tr>
          <tr class="cel2">
            <th>Date de naissance : </th>
            <td>
              <?php echo $form['naissance']->renderError(); ?>
              <?php echo $form['naissance']->render(array('tabindex' => '30')); ?>
            </td>
          </tr>
          <tr class="cel1">
            <th style="text-align:left;"><?php echo $form['sexe']->renderLabel() ?></th>
            <td>
              <?php echo $form['sexe']->renderError(); ?>
              <?php echo $form['sexe']->render(array('tabindex' => '40')); ?>
            </td>
          </tr>
          <tr class="cel2">
            <td colspan="2"><a href="<?php echo url_for('@citoyen?slug=' . $sf_user->getAttribute('slug')) ?>" tabindex="60">Annuler</a> <input type="submit" value="Valider" tabindex="50" /></td>
          </tr>
		  <tr class="cel1">
            <th>Votre Avatar</th>
            <td>
              <a href="<?php echo url_for('@upload_avatar'); ?>" tabindex="70"><img src="<?php echo url_for('@photo_citoyen?slug='.$user->slug); ?>" alt="Votre photo" /><br/>Changer d'avatar</a>
            </td>
          </tr>
          <tr class="cel2">
            <th>Votre mot de passe</th>
            <td><a href="<?php echo url_for('@editpassword_citoyen'); ?>" tabindex="80">Changer</a></td>
          </tr>
          <tr class="cel1">
            <th colspan="2">
              <a href="<?php echo url_for('@delete_citoyen?token=' . $sf_user->getAttribute('token')) ?>" tabindex="90" onclick="javascript:if(!confirm('Supprimer votre compte ?')) return false;">Supprimer votre compte</a>
            </th>
          </tr>
        </table>
        </form>
        <br />
      </div>