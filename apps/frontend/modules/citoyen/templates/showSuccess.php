<div class="temp">
<h1><?php echo $Citoyen->login; ?></h1>
<?php if ($sf_user->hasFlash('notice')): ?>
  <p class="notice"><?php echo $sf_user->getFlash('notice') ?></p>
<?php endif; ?>
<?php if ($Citoyen->photo) { echo image_tag($Citoyen->slug.'.jpg', 'alt=Photo de '.$Citoyen->login); } ?>
<ul>
<?php
if (!empty($Citoyen->activite)) { $activite = $Citoyen->activite; }
else { $activite = 'non renseigne'; }
?>
<?php echo '<li>Activite : '.$activite.'</li>'; ?>
<?php
if (!empty($Citoyen->nom_circo)) { $circonscription = $Citoyen->nom_circo.' '.$Citoyen->num_circo; }
else { $circonscription = 'Pour ajouter cette information, naviguez jusqu\'a la page de votre depute et cliquez sur ICONE '; }
?>
<?php echo '<li>Circonscription : '.$circonscription.'</li>'; ?>
</ul>
  <p>
  <a href="<?php echo url_for('@edit_citoyen'); ?>">Modifier votre profil</a><br />
  <a href="<?php echo url_for('@activation_citoyen?activation_id='.$Citoyen->activation_id); ?>">Lien d'activation qui sera contenu dans l'email</a><br />
  <a href="<?php echo url_for('@delete_citoyen') ?>">Supprimer votre compte</a>
  </p>
</div>