<?php
include("config.inc");

$slug = preg_replace('/[^a-z0-9\-]/i', '', $_GET['slug']);

$sql = "SELECT num_mois, nb_presence, nb_reunion_commission, nb_absences, has_excuse, retenues_nb * $montant as retenues_euros, expl_excuse FROM absences WHERE slug = '$slug';";
$res = mysql_query($sql); 

$mois = array('10' => 'Octobre', '11' => 'Novembre', '12' => 'Décembre' );
echo "<div class='table_plus' id='table_$slug' style='display: none;clear:both;'>";
echo "<div style='float: left; margin-top: 0px;margin-left: 10px; margin-right: 10px'><a href='http://www.nosdeputes.fr/".$slug."'><img height=60 width=46 src='http://www.nosdeputes.fr/depute/photo/".$slug."/60' /><br/>Voir son activité<br/>sur NosDeputes.fr</a></div>";
echo "<table><tr><th>Mois</th><th>Présences</th><th>Réunions</th><th>Absences</th><th>Excuses</th><th>Retenues<br/>théoriques</th><th></th></tr>";
while ($row = mysql_fetch_assoc($res))
{
  echo "<tr><td class='titre'><a href='http://www.nosdeputes.fr/$slug/presences/commission#date_2009_".$row['num_mois']."'>";
  echo $mois[$row['num_mois']];
  echo "</a></td><td>";
  echo $row['nb_presence'];
  echo "</td><td>";
  echo $row['nb_reunion_commission'];
  echo "</td><td>";
  echo $row['nb_absences'];
  echo "</td><td>";
  echo $row['has_excuse'];
  echo "</td><td>";
  printf('%.2d', $row['retenues_euros']);
  echo " €</td><td>";
  echo $row['expl_excuse'];
  echo "</td></tr>";
}
echo "</table></div>";
?>
<script><!--
$("#table_<?php echo $slug; ?>").slideDown();
--></script>
