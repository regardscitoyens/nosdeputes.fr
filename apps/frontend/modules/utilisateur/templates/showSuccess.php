<div class="temp">
<h1><?php echo $Utilisateur->login; ?></h1>
<?php if ($Utilisateur->photo) { echo image_tag($Utilisateur->slug.'.jpg', 'alt=Photo de '.$Utilisateur->login); } ?>
<ul>
<?php echo '<li>Profession/Occupation : '.$Utilisateur->profession.'</li>'; ?>
<?php echo '<li>Circonscription : '.$Utilisateur->circo.' '.$Utilisateur->circo_num.'</li>'; ?>
</ul>
<p>
<a href="<?php echo url_for('utilisateur/edit?slug='.$Utilisateur->slug) ?>">Modifier votre profil</a>
</p>
</div>