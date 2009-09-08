<h1>Créer un compte-rendu citoyen</h1>
<?php
if (isset($seances)) {
  echo '<h2>Nous avons trouvé des séances correspondants à vos critères<br/>Veuillez selectionner celle à laquelle vous avez participé</h2><ul>';
  foreach($seances as $s) {
    echo '<li><a href="'.url_for('@compterendu_new?object_id='.$s['id']).'">Séance du '.myTools::displayDate($s['date']).', '.$s['moment'].'</a></li>';
  }
  echo '</ul>';
  return;
}
if (isset($found_sections)) {
  echo '<h2>Plusieurs projets correspondent à vos critères, veuillez en selectionner un</h2><ul>';
  foreach($found_sections as $s) {
    echo '<li><a href="?section_id='.$s['id'].'&cdate='.$form->getValue('date').'">'.$s['titre'].'</a></li>';
  }
  echo '</ul>';
  return ;
}
?>
<p>Pour créer un compte rendu citoyen, vous devez d'abord nous indiquer à quelle séance vous avez assisté</p>
<form>
<p>Indiquez la date à laquelle vous avez assisté à une séance publique en hémicycle :</p>
<table>
<?php
echo $form;
?>
</table>
<input type="submit" value="Chercher"/>
</form>
<p><strong>Ou</strong> Indiquez un mot du projet de loi auquel vous avez assisté :</p>
<form><table>
<tr><th>Nom du projet de loi</th><td><input name='section' value="<?php echo $section; ?>"/></td></tr>
</table>
<input type="submit" value="Chercher"/>
</form>