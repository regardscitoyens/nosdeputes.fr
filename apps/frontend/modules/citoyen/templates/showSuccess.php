<div class="temp">
<h1><?php echo $Citoyen->login; ?></h1>
<ul>
<?php echo '<img src="'.url_for('@photo_citoyen?slug='.$Citoyen->slug).'" alt="Photo de '.$Citoyen->login.'"/>';
if (!empty($Citoyen->activite)) { $activite = $Citoyen->activite; } else { $activite = 'non renseigné'; }
  echo '<li>Activité : '.$activite.'</li>';
  echo '<li>Membre depuis le : '.myTools::displayDate($Citoyen->created_at).'</li>';
if (!empty($Citoyen->url_site)) { echo '<li><a href="'.$Citoyen->url_site.'" rel="nofollow">Site web</a></li>'; }
?></ul>
<?php
if ($sf_user->getAttribute('user_id') == $Citoyen->id)
{ ?>
<p>
  <a href="<?php echo url_for('@edit_citoyen'); ?>">Modifier votre profil</a><br />
  <a href="<?php echo url_for('@delete_citoyen?token=' . $sf_user->getAttribute('token')) ?>">Supprimer votre compte</a>
</p>
<?php
} ?>
</div>
<div class="temp">
<h2>Ses commentaires</h2>  
<?php include_component('commentaire', 'showcitoyen', array('id'=>$Citoyen->id)); ?>
</div>
