<div class="temp">
<h1><?php echo $Citoyen->login; ?></h1>
<?php if ($Citoyen->photo) { echo image_tag($Citoyen->slug.'.jpg', 'alt=Photo de '.$Citoyen->login); } ?>
<ul>
<?php
if (!empty($Citoyen->activite)) { $activite = $Citoyen->activite; }
else { $activite = 'non renseigné'; }
?>
<?php echo '<li>Activité : '.$activite.'</li>'; ?>
<?php
if (($sf_user->getAttribute('login') == $Citoyen->login) and empty($Citoyen->nom_circo))  { $circonscription = 'Pour ajouter cette information, naviguez jusqu\'a la page de votre député et cliquez sur ICONE '; }
else if (!empty($Citoyen->nom_circo)) { $circonscription = $Citoyen->nom_circo.' '.$Citoyen->num_circo; }
else { $circonscription = 'non renseigné'; }
?>
<?php echo '<li>Circonscription : '.$circonscription.'</li>'; ?>
</ul>
<?php
if ($sf_user->getAttribute('login') == $Citoyen->login)
{ ?>
  <p>
  <a href="<?php echo url_for('@edit_citoyen'); ?>">Modifier votre profil</a><br />
  <a href="<?php echo url_for('@activation_citoyen?activation_id='.$Citoyen->activation_id); ?>">Lien d'activation qui sera contenu dans l'email</a><br />
  <a href="<?php echo url_for('@delete_citoyen') ?>">Supprimer votre compte</a>
  </p>
<?php
} ?>
</div>