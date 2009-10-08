<h1>Liste des Citoyens</h1>
<?php if (!$sf_user->isAuthenticated()) { ?>
<p><strong><a href="<?php echo url_for('citoyen/new') ?>">S'inscrire</a></strong></p>
<?php } ?>
<p>
 <?php foreach ($citoyens_list as $citoyen): ?>
<a href="<?php echo url_for('@citoyen?slug='.$citoyen->getSlug()); ?>">
<?php echo $citoyen->getLogin(); ?></a> 
<?php if($citoyen->getActivite()) { echo '('.$citoyen->getActivite().')'; } ?>
<br/>
<?php endforeach; #é ?>
</p>