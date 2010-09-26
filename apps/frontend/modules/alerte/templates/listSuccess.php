<h1>Liste de mes alertes</h1>
<p>Les alertes emails permettent d'être tenu informé dès leur publication sur NosDeputes.Fr des documents parlementaires contenant des mots clés précis. Voici la liste des alertes auxquelles vous êtes abonné(e). Cette interface vous permet de les éditer et les supprimer</p>
<?php if (count($alertes)) : ?>
<table class="list">
<tr><th>Type d'alerte</th><th>Envoyée au maximum</th><th>Dernier envoi</th></tr>
<?php //'
$period = array('HOUR' => 'heure', 'DAY' => 'jour', 'WEEK' => 'semaine', 'MONTH' => 'mois');
foreach($alertes as $a) 
{
  echo "<tr><td>";
  echo link_to($a->getTitre(), 'alerte/edit?verif='.$a->verif);
  echo "</td><td>une fois par ".$period[$a->getPeriod()]."</td><td>".$a->getLastMail();
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