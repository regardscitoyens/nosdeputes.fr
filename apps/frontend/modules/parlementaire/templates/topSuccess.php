<table>
<tr><th></th><th></th><th colspan="2">Commission</th><th colspan="2">Hémicycle</th><th colspan="3">Amendements</th><th colspan="2">Questions</th></tr>
<tr><th></th>
<?php 
$title = array('semaine' => 'semaine',
	       'commission_presences' => 'présence',
	       'commission_interventions'=> 'interventions',
	       'hemicycle_interventions'=>'interventions<br/>longues',
	       'hemicycle_invectives'=>'interventions<br/>courtes',
	       'amendements_signes' => 'signés',
	       'amendements_adoptes'=>'adoptés',
	       'amendements_rejetes' => 'rejetés',
	       'questions_ecrites' => 'écrites',
	       'questions_orales' => 'orales');
$last = end($tops); $i = 0; foreach(array_keys($last[0]->getTop()) as $key) { $i++ ;?>
  <th><a href="?sort=<?php echo $i; ?>"><?php echo $title[$key]; ?></a></th>
<?php } ?></tr>
<?php foreach($tops as $t) { ?>
<tr><td>
<img src="<?php echo url_for('@photo_parlementaire?slug='.$t[0]->slug);?>/30" width='23' height='30'/><br/>
<? echo link_to($t[0]->nom, '@parlementaire?slug='.$t[0]->slug); ?></td>
<?php for($i = 1 ; $i < count($t) ; $i++) { ?>
     <td<?php echo $t[$i]['style']; ?>><?php 
     if (preg_match('/\./', $t[$i]['value'])) {
       printf('%02d', $t[$i]['value']);
     } else{
       echo $t[$i]['value']; 
     }
?></td>
<?php } ?>
<?php } ?>
</table>