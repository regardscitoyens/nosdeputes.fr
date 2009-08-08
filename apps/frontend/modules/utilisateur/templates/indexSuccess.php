<h1>Liste des utilisateurs</h1>

<table>
  <thead>
    <tr>
      <th>Login</th>
      <th>Profession</th>
      <th>Circonscription</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($utilisateur_list as $utilisateur): ?>
    <tr>
      <td><a href="<?php echo url_for('utilisateur/edit?slug='.$utilisateur->getSlug()) ?>"><?php echo $utilisateur->getLogin() ?></a></td>
      <td><?php echo $utilisateur->getProfession() ?></td>
      <td><?php echo $utilisateur->getCirco().' '.$utilisateur->getCircoNum() ?></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>

  <a href="<?php echo url_for('utilisateur/new') ?>">S'inscrire</a>
