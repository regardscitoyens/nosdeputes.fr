<?php use_helper('Text') ?>
<div class="boite_citoyen">
  <div class="b_c_h"><div class="b_c_hg"></div><div class="b_c_hd"></div></div>
    <div class="b_c_cont">
      <div class="b_c_photo">
<?php   if (!$user->photo)
          echo image_tag('xneth/avatar_citoyen.png', array('alt' => 'Photo de '.$user->login));
        else echo '<img src="'.url_for('@photo_citoyen?slug='.$user->slug).'" alt="Photo de '.$user->login.'" />';
?>
      </div>
      <div class="b_c_right"><?php echo ucfirst(preg_replace('/membre/', 'inscrit', $user->role)).($user->sexe === "F" ? "e" : "").' depuis&nbsp;<br/>le '.myTools::displayDate($user->created_at); ?></div>
      <div class="b_c_text">
        <h1><?php echo $user->login; ?></h1>
        <?php if (!empty($user->activite)) $activite = $user->activite;
          else $activite = '(Activité non-renseignée)'; ?>
        <p><?php echo $activite; 
          if ($user->naissance) echo '<br/>'.myTools::getAge($user->naissance).'&nbsp;ans'; ?></p>
        <?php if (!empty($user->url_site)) {
          $url = html_entity_decode(strip_tags($user->url_site), ENT_NOQUOTES, "UTF-8");
          echo '<div class="b_c_link"><a href="'.$url.'" rel="nofollow">'.$url.'</a></div>';
        } ?>
      </div>
      <?php if ($sf_user->getAttribute('user_id') == $user->id) {
	    echo '<div class="b_c_edit"><a href="'.url_for('alerte/list').'">Gérer mes alertes</a> &mdash; ';
	    echo '<a href="'.url_for('@edit_citoyen').'">Modifier mon profil</a></div>';
	  }
        ?>
    </div>
  <div class="b_c_b"><div class="b_c_bg"></div><div class="b_c_bd"></div></div>
</div>
<div class="stopfloat"></div>
<?php include_component('commentaire', 'showAllCitoyen', array('id'=>$user->id)); ?>
