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
$class = array('parl' => 'p',
	       'semaine' => 'w',
	       'commission_presences' => 'cp',
	       'commission_interventions'=> 'ci',
	       'hemicycle_interventions'=>'hl',
	       'hemicycle_invectives'=>'hc',
	       'amendements_signes' => 'as',
	       'amendements_adoptes'=>'aa',
	       'amendements_rejetes' => 'ar',
	       'questions_ecrites' => 'qe',
	       'questions_orales' => 'qo');
?><div class="liste_deputes_tags">
<style>
  td, tr{padding: 0px; margin: 0px, border: 0px;}
.p{width: 140px;}
.w{width: 65px;}
.cp{width: 75px;}
.ci{width: 85px;}
.hl{width: 101px;}
.hc{width: 101px;}
.as{width: 51px;}
.aa{width: 61px;}
.ar{width: 51px;}
.qe{width: 51px;}
.qo{width: 51px;}
.tr_odd td { border-right: 1px #999999 solid;}
.tr_odd td.qo { border-right: 0px #999999 solid;}
</style>
<h1>Synthèse générale des députés ayant au moins 6 mois de mandat</h1>
<h2>Sur les 12 derniers</h2>
<div class="synthese">
<table>
  <tr><th class="<?php echo $class['parl']; ?>">&nbsp;</th><th></th><th colspan="2">Commission</th><th colspan="2">Hémicycle</th><th colspan="3">Amendements</th><th colspan="2">Questions</th></tr>
  <tr><th class="<?php echo $class['parl']; ?>">&nbsp;</th>
<?php
$ktop = array('');
$last = end($tops); $i = 0; foreach(array_keys($last[0]->getTop()) as $key) { $i++ ; array_push($ktop, $key);?>
<th class="<?php echo $class[$key]; ?>"><?php echo link_to($title[$key], $top_link.'sort='.$i); ?></a></th>
<?php } ?></tr></table>
<div height="500px" style="height: 500px;overflow: scroll; overflow: auto;">
<table>
<?php $cpt = 0; foreach($tops as $t) { $cpt++;?>
<tr<?php if ($cpt %2) echo ' class="tr_odd"'?>><td class="<?php echo $class['parl']; ?>"><a name="<?php echo $t[0]->slug; ?>"></a><img src="<?php echo url_for('@photo_parlementaire?slug='.$t[0]->slug);?>/30" width='23' height='30'/><br/>
<? echo link_to($t[0]->nom, '@parlementaire?slug='.$t[0]->slug); ?></td>
<?php for($i = 1 ; $i < count($t) ; $i++) { ?>
     <td<?php echo $t[$i]['style']; ?> class="<?php echo $class[$ktop[$i]]; ?>"><?php 
     if (preg_match('/\./', $t[$i]['value'])) {
       printf('%02d', $t[$i]['value']);
     } else{
       echo $t[$i]['value']; 
     }
?></td>
<?php } ?></tr>
<?php }?>
</table>
</div>
</div>
</div>