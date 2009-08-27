<div class="temp">
<h1><?php echo $Citoyen->username; ?></h1>
<?php if ($sf_user->hasFlash('notice')): ?>
  <p class="notice"><?php echo $sf_user->getFlash('notice') ?></p>
<?php endif; ?>
<?php if ($Citoyen->photo) { echo image_tag($Citoyen->slug.'.jpg', 'alt=Photo de '.$Citoyen->username); } ?>
<ul>
<?php echo '<li>Profession/Occupation : '.$Citoyen->profession.'</li>'; ?>
<?php echo '<li>Circonscription : '.$Citoyen->nom_circo.' '.$Citoyen->num_circo.'</li>'; ?>
</ul>
<?php
if($sf_user->isAuthenticated() and ($sf_user->getGuardUser()->id == $Citoyen->sf_guard_user_id))
{
?>
  <p>
  <a href="<?php echo url_for('@edit_citoyen') ?>">Modifier votre profil</a><br />
  <a href="<?php echo url_for('@delete_citoyen') ?>">Supprimer votre profil</a>
  </p>
<?php
}
?>
</div>