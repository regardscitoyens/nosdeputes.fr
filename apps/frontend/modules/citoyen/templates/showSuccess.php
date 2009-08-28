<div class="temp">
<h1><?php echo $Citoyen->username; ?></h1>
<?php if ($sf_user->hasFlash('notice')): ?>
  <p class="notice"><?php echo $sf_user->getFlash('notice') ?></p>
<?php endif; ?>
<?php if ($Citoyen->photo) { echo image_tag($Citoyen->slug.'.jpg', 'alt=Photo de '.$Citoyen->username); } ?>
<ul>
<?php echo '<li>Profession/Occupation : '.$Citoyen->profession.'</li>'; ?>
<?php
if (!empty($Citoyen->nom_circo))
{
  $circonscription = $Citoyen->nom_circo.' '.$Citoyen->num_circo;  
}
else
{
  $circonscription = 'Pour ajouter cette information, veuillez naviguer jusqu\'a la page de votre depute et cliquez sur "selectionner comme mon depute" ';
}
?>
<?php echo '<li>Circonscription : '.$circonscription.'</li>'; ?>
</ul>
<?php
if($sf_user->isAuthenticated() and ($sf_user->getGuardUser()->id == $Citoyen->sf_guard_user_id))
{
?>
  <p>
  <a href="<?php echo url_for('@edit_citoyen'); ?>">Modifier votre profil</a><br />
  <a href="<?php echo url_for('@activation_citoyen?activation_id='.$Citoyen->activation_id); ?>">Lien d'activation qui sera contenu dans l'email</a><br />
  <a href="<?php echo url_for('@delete_citoyen') ?>">Supprimer votre compte</a>
  </p>
<?php
}
?>
</div>