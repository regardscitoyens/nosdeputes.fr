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
?></ul>
<?php
if ($sf_user->getAttribute('user_id') == $user->id)
{ ?>
<p>
  <a href="<?php echo url_for('@edit_citoyen'); ?>">Modifier votre profil</a>
</p>
<?php
} ?>
      </div>
    </div>
  <div class="b_c_b"><div class="b_c_bg"></div><div class="b_c_bd"></div></div>
</div>
<div class="stopfloat"></div>
<h2>Ses commentaires</h2>  
<?php include_component('commentaire', 'showcitoyen', array('id'=>$user->id)); ?> 
