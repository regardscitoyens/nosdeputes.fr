<?php if ($submit == 'Créer') : ?>
<h1>Création d'une alerte email</h2>
<?php $sf_response->setTitle('Création d\'une alerte email');
else :?>
<h1>Modification d'une alerte email</h2>
<?php $sf_response->setTitle('Modification d\'une alerte email');
endif;
?>
<form method="POST">
<table><?php
if ($form->getObject()->citoyen_id) {
echo "<tr><th>Email</th><td>".$form->getObject()->Citoyen->email."</td></tr>";
}
if ($form->getObject()->no_human_query) {
echo "<tr><th>Alerte portant sur</th><td>".$form->getObject()->titre."</td></tr>";
}
if ($f = $form->getObject()->filter) {
echo "<tr><th>Filtré sur</th><td>".preg_replace('/[\&,] ?/', ', ', preg_replace('/[^=\&\,]+=/i', '', strtolower(urldecode($f))))."</td></tr>";
}
echo $form;
?>
<tr><th></th><td><input type="submit" value="<?php echo $submit; ?>"> <?php if ($submit != 'Créer') echo link_to('Supprimer', 'alerte/delete?verif='.$form->getObject()->verif);?></td></tr>
</table>
</form>