<?php
include("config/config.inc");
$id = preg_replace('/[^0-9\-]/i', '', $_GET['id']);

$sql = "SELECT slug, id, annee, mois, nb_presences, nb_commission, nb_sanctions * $montant as retenues_euros, excuses FROM detail WHERE id = '$id';";
$res = mysql_query($sql);
$row = mysql_fetch_assoc($res);

$slug = $row['slug'];

$mois = array('01' => 'Janvier', '02' => 'Février', '03' => 'Mars', '04'=>'Avril', '05' => 'Mai', '06' => 'Juin', '10' => 'Octobre', '11' => 'Novembre', '12' => 'Décembre' );
echo "<div class='table_plus' id='table_$slug' style='display: none;clear:both;'>";
echo "<div style='float: left; margin-top: 0px;margin-left: 10px; margin-right: 10px'><a href='/".$slug."'><img height=60 width=46 src='/depute/photo/".$slug."/60' /><br/>Voir son activité<br/>sur NosDeputes.fr</a></div>";
echo "<table><tr><th>Mois</th><th>Présences</th><th>Réunions</th><th>Retenues</th><th></th></tr>";
do
{
  $row['mois'] = sprintf('%02d', $row['mois']);
  echo "<tr><td class='titre'><a href='/".$row['slug']."/presences/commission#date_".$row['annee']."_".$row['mois']."'>";
  echo $mois[$row['mois']];
  echo " ".$row['annee'];
  echo "</a></td><td>";
  echo $row['nb_presences'];
  echo "</td><td>";
  echo $row['nb_commission'];
  echo "</td><td>";
  printf('%.2d', $row['retenues_euros']);
  echo " €</td><td>";
  echo $row['excuses'];
  echo "</td></tr>";
}
 while ($row = mysql_fetch_assoc($res));
echo "</table></div>";
?>
<script><!--
$("#table_<?php echo $slug; ?>").slideDown();
--></script>
