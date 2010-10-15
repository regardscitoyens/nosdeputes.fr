<?php if (isset($alertes)) { ?>
<h1>Liste de mes alertes</h1>
<?php $sf_response->setTitle('Liste de mes alertes mails');
?><p>Les alertes emails permettent d'être tenu informé dès leur publication sur NosDeputes.Fr des documents parlementaires contenant des mots clés précis. Voici la liste des alertes auxquelles vous êtes abonné(e). Cette interface vous permet de les éditer et les supprimer</p>
<?php if (count($alertes)) : ?>
<table class="list">
<?php 
$filter = 0;
foreach($alertes as $a) 
{
if ($a->getFilter())
$filter = 1;
break;
}
?>
<tr><th>Type d'alerte</th><?php if ($filter) echo '<th>Filtre</th>'; ?><th>Envoyée au maximum</th><th>Dernier envoi</th></tr>
<?php //'
$period = array('HOUR' => 'heure', 'DAY' => 'jour', 'WEEK' => 'semaine', 'MONTH' => 'mois');
foreach($alertes as $a) 
{
  echo "<tr><td>";
  echo link_to($a->getTitre(), 'alerte/edit?verif='.$a->verif);
  echo "</td><td>";
  if ($filter) {
    echo ($f = $a->getFilter()) ? preg_replace('/[&,] ?/', ', ', preg_replace('/[^=\&\,]+=/i', '', urldecode(strtolower($f)))) : " - ";
    echo "</td><td>";
  }
  echo "une fois par ".$period[$a->getPeriod()]."</td><td>";
  echo ($a->getNextMail()) ? $a->getLastMail() : " - ";
  echo "</td><td>";
  echo link_to('<img src="/images/xneth/remove.png"/>', 'alerte/delete?verif='.$a->verif);
  echo"</td></tr>";
}
?>
</table>
<?php else : ?>
<p>Vous n'avez pas d'alerte définie</p>
<?php endif; ?>
<p><?php echo link_to('Créer une nouvelle alerte', 'alerte/create'); ?></p>
<?php } else { ?>
<p>Vous n'êtes pas connecté à votre compte de citoyen, vous pouvez le faire en haut à droite de chaque page ou <a href="<?php echo url_for('@signin'); ?>">en cliquant sur ce lien</a>.</p>
<?php } ?>
