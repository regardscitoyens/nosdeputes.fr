<?php
include("header.html");
?>
<link rel="stylesheet" type="text/css" media="screen" href="style.css" />
<script src="jquery.js"></script>
<script><!--
$(document).ready(function() {
    $(".plus_link").click(
			 function() {
			   $(".table_plus").slideUp();
			   $("#plus_"+$(this).attr('id')).load("detail.php?slug="+$(this).attr('id'));
			   
			   return false;
			 });
  });

-->
</script>
<?php
include('config/config.inc');?>
<h1>Application du règlement relatif à la présence en commission<br><span style="color:red;font-size: 0.9em; opacity:0.8;">Octobre - Décembre 2009</span></h1>
<div class='etude'>
<p>Cette étude vise à évaluer l'incidence des modifications du règlement sur l'agenda des députés et tout particulièrement sur leur présence en commission. Dorénavant, au-delà de 2 absences par mois dans sa commission permanente, un député peut se voir retirer 25% de ses indemnités de fonction par absence supplémentaire.</p>
<p style="color:red">Suite à cette première étude, le règlement a réellement été appliqué à partir de février 2010 comme l'a démontré notre <a href="/confiseurs/2010/">étude de juillet 2010</a>. Nous avons renouvelé ce travail pour la <a href="/confiseurs/2011/">session 2010 - 2011</a>.</p>
</div>
<h2>Incidence des modifications du règlement sur la présence moyenne</h2>
<a name='incidence'></a>
<div class='etude'>
<p>En dénombrant pour l'ensemble des députés les présences en commission enregistrées au Journal Officiel, nous pouvons proposer une estimation de l'évolution de la présence d’une session sur l’autre. Pour ce faire, nous avons calculé sur le premier trimestre de chaque session (octobre, novembre et décembre) le total des présences de députés le mercredi pour les 3 dernières années. On remarque ainsi une augmentation de 54% depuis la modification du règlement.</p>
<p></p>
<table class="stats" style="margin:auto">
  <tr><th class="empty">&nbsp;</th><th>Cumul des députés<br/>présents un mercredi</th><th colspan="2">Moyenne des présences<br/>le mercredi par député</th><th class="empty">&nbsp;</th></tr>
<tr><td>2007</td><td>2156</td><td>3,7</td><td>1 mercredi sur 3</td></tr>
<tr><td>2008</td><td>2374</td><td>4,1</td><td>1 mercredi sur 3</td></tr>
<tr><td>2009</td><td>3650</td><td>6,3</td><td>1 mercredi sur 2</td></tr></table>

<p>Cette assiduité renforcée n'est cependant pas limitée aux séances obligatoires du mercredi matin. On peut remarquer que les députés sont également plus assidus aux réunions de commissions se déroulant les autres jours de la semaine. Ils assistent en moyenne en 2009 à 23&nbsp;% plus de réunions les autres jours que le mercredi.</p>
<table class="stats" style="margin:auto">
  <tr><th class="empty">&nbsp;</th><th>Cumul des députés<br/>présents un autre jour<br/>que le mercredi</th><th>Moyenne des présences<br/>les autres jours<br/>que le mercredi</th></tr>
<tr class><td>2007</td><td>3243</td><td>5,6</td></tr>
<tr><td>2008</td><td>3212</td><td>5,6</td></tr>	
<tr><td>2009</td><td>3945</td><td>6,8 </td></tr>
</table>
<p>D'autres facteurs que cette modification du règlement seraient donc à envisager pour expliquer ces transformations&nbsp;.</p>
<p>Si l'on regarde plus précisément ces données député par député, on peut recenser les économies que pourrait réaliser l'Assemblée nationale en appliquant son nouveau règlement&nbsp;:</p>
<br/>
</div>
<h2>Les députés susceptibles d'être pénalisés faute de présences suffisantes</h2>
<a name='deputes'></a>
<p class="etude"><b>Cette partie de l'étude a été mise à jour en fonction des remarques faites par le président Bernard Accoyer. Suite à notre publication, la présidence de l'Assemblée nous a en effet appris que les sanctions financières ne seraient appliquées qu'à partir du mois de décembre (<a href="http://www.regardscitoyens.org/absences-en-commissions-accoyer-confirme-les-sanctions/">plus d'info</a>)</b>.<br/>En conséquence, notre tableau général n'indique plus le total des pénalités prévues pour les mois d'octobre et novembre.</p>
<p></p>
<p class="etude"><small>Pour trier sur un critère, cliquez sur le titre de la colonne voulue.</small><br/></p>
<?php
//
$ids = $_GET['sort'];
switch ($ids)
{
 case '1' :
   $sort = " ORDER BY nb_presence";
   break;
 case '2' :
   $sort = " ORDER BY nb_presence_max DESC";
   break;
 case '3' :
   $sort = " ORDER BY retenues_euros DESC";
   break;
 default :
   $sort = " ORDER BY nom_de_famille";
   $ids = 0;
}

$sql = "SELECT nom, slug, groupe, sum(nb_presence) as nb_presence, sum(nb_reunion_commission) as nb_presence_max, sum(retenues_nb) * $montant as retenues_euros from absences GROUP BY nom $sort";

$res = mysql_query($sql); 

echo "<div class='etude'><table width=600>";
echo "<tr><th class='n'>";
if ($ids != 0) echo '<a href="index.php#deputes">';
echo "Nom (Groupe)";
if ($ids != 0) echo '</a>';
echo "</th><th class='p'>";
if ($ids != 1) echo '<a href="index2.php?sort=1#deputes">';
echo "Présences<br/>en commission<br/>le mercredi";
if ($ids != 1) echo '</a>';
echo "</th><th class='r'>";
if ($ids != 2) echo '<a href="index2.php?sort=2#deputes">';
echo "Réunions de la<br/>commission le<br/>mercredi matin";
if ($ids != 2) echo '</a>';
echo "</th><th class='e'>";
//if ($ids != 3) echo '<a href="index2.php?sort=3#deputes">';
echo "Retenues sur<br/>indémnités<br/>(décembre)";
//if ($ids != 3) echo '</a>';
echo "</th><th class='l'>&nbsp;</th></tr></table>";
?>
<div height="500px" style="height: 500px;overflow: scroll; overflow: auto;"><table width="600">
<?php
$cpt=0;
while ($row = mysql_fetch_assoc($res))
{
  echo "<tr class='hl".$cpt."'>";
  echo '<td class="n border titre">'.$row['nom'].' ('.$row['groupe'].')</td>';
  echo "<td class='p'>".$row['nb_presence']."</td>";
  $sql = "SELECT retenues_nb * $montant as retenues_euros from absences WHERE num_mois = 12 AND nom = \"".$row['nom']."\"";
  $res2 = mysql_query($sql);
  $row2 = mysql_fetch_assoc($res2);
  echo "<td class='r'>".$row['nb_presence_max']."</td>";
  echo "<td class='e'>".sprintf('%.02d', $row2['retenues_euros'])." €</td>";
  echo "<td class='l link_".$row['slug']."'><a href='#' id=".$row['slug']." class='plus_link'>Plus d'info</a></td>";
  echo "</tr>";
  echo '<tr class="hl'.$cpt.'"><td class="border" id="plus_'.$row['slug'].'" colspan="5"></td></tr>';
  $cpt++;
  if ($cpt > 1) $cpt = 0;
}
echo "</table></div><br/></div>";
?>
<h2>Méthodologie</h2>
<div class="etude">
<p>Cette étude du <a href="http://www.regardscitoyens.org">Collectif Regards Citoyens</a> se base sur les données de NosDéputés.fr récupérées au Journal Officiel et sur les critères précis définis à <a href="http://www.assemblee-nationale.fr/connaissance/reglement.asp#P719_63307">l'article 42 alinéa 3 du règlement de l'Assemblée nationale</a>.</p>
<p>Pour sélectionner les députés susceptibles de voir leurs indemnités réduites, nous avons pour chaque député&nbsp;:</p>
<ul>
<li>calculé le nombre de présences lors des 3 premiers mois de la session à au moins une commission du mercredi (matin ou après midi)&nbsp;;</li>
<li>comparé ce total avec le nombre de réunions de sa commission permanente&nbsp;;</li>
<li>exclu du champ de notre étude&nbsp:<ul>
<li>les membres du bureau (à l'exception des secrétaires) (<a href="http://www.assemblee-nationale.fr/connaissance/reglement.asp#P718_63191">article 42-3</a>)&nbsp;;</li>
<li>les présidents de groupe (<a href="http://www.assemblee-nationale.fr/connaissance/reglement.asp#P718_63191">article 42-3</a>)&nbsp;;</li>
<li>les députés des DOM/TOM (<a href="http://www.assemblee-nationale.fr/connaissance/reglement.asp#P718_63191">article 42-3</a>)&nbsp;;</li>
<li>les députés appartenant aux assemblées internationales ou européennes qui ont des travaux à la même date (<a href="http://www.assemblee-nationale.fr/connaissance/reglement.asp#P680_57502">article 38-2</a>).</li>
</ul></li></ul>
<br/></div>
<?php
include("footer.html");
