<div class="temp">
<h1>Liste des Citoyens</h1>
<?php if ($sf_user->hasFlash('notice')): ?>
  <p class="notice"><?php echo $sf_user->getFlash('notice') ?></p>
<?php endif; ?>
<table>
  <thead>
    <tr>
      <th>Nom d'utilisateur</th>
      <th>Activité</th>
      <th>Circonscription</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($citoyens_list as $citoyen): ?>
    <tr>
      <td><a href="<?php echo url_for('@citoyen?slug='.$citoyen->getSlug()) ?>"><?php echo $citoyen->getLogin() ?></a></td>
      <td><?php echo $citoyen->getActivite() ?></td>
      <td><?php echo $citoyen->getNomCirco().' '.$citoyen->getNumCirco() ?></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<?php
if (!$sf_user->isAuthenticated())
{ ?>
<p>
  <strong><a href="<?php echo url_for('citoyen/new') ?>">S'inscrire</a></strong>
</p>
<?php
}
?>
</div>