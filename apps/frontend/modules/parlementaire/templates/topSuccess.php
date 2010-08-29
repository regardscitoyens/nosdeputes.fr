<h1>Synthèse générale des députés ayant au moins 6 mois de mandat</h1>
<h2 style="text-align: center">(sur les 12 derniers mois)</h2>
<?php 
$sf_response->setTitle('Synthèse générale des députés');
$title = array('semaine' => 'd\'activité',
	       'commission_presences' => 'séances',
	       'commission_interventions'=> 'interventions',
	       'hemicycle_interventions'=>'interventions<br/>longues',
	       'hemicycle_interventions_courtes'=>'interventions<br/>courtes',
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
	       'hemicycle_interventions_courtes'=>'hc',
	       'amendements_signes' => 'as',
	       'amendements_adoptes'=>'aa',
	       'amendements_rejetes' => 'ar',
	       'questions_ecrites' => 'qe',
	       'questions_orales' => 'qo');
?>
<div class="liste_deputes_tags">
<div class="synthese">
<table>
  <tr>
    <th class="<?php echo $class['parl']; ?>">&nbsp;</th>
	<th class="<?php if ($sort == 1) echo 'tr_odd';?>"><?php echo link_to('Semaines', $top_link.'sort=1'); ?></th>
	<th colspan="2" class="<?php if ($sort == 2 || $sort == 3) echo 'tr_odd';?>">Commission</th>
	<th colspan="2" class="<?php if ($sort == 4 || $sort == 5) echo 'tr_odd';?>">Hémicycle</th>
	<th colspan="3" class="<?php if ($sort == 6 || $sort == 7 || $sort == 8) echo 'tr_odd';?>">Amendements</th>
	<th colspan="2" class="<?php if ($sort == 9 || $sort == 10) echo 'tr_odd';?>">Questions</th>
  </tr>
  <tr>
    <th class="<?php echo $class['parl']; ?>">&nbsp;</th><?php
    $last = end($tops); $i = 0; 
    foreach($ktop as $key) { 
      $i++ ; 
    ?>
    <th class="<?php echo $class[$key]; if ($sort == $i) echo ' tr_odd'?>"><?php echo link_to($title[$key], $top_link.'sort='.$i); ?></th>
	<?php 
	} ?>
  </tr>
</table>
<?php array_unshift($ktop, ''); ?>
<div class="tableau_synthese">
<table>
  <?php 
  $cpt = 0; 
  foreach($tops as $t) {
    $cpt++;?><tr<?php if ($cpt %2) echo ' class="tr_odd"'?>>
	<td class="<?php echo $class['parl']; ?>"><a name="<?php echo $t[0]['slug']; ?>" href="<?php echo url_for('@parlementaire?slug='.$t[0]['slug']); ?>"></a><?php echo link_to($t[0]['nom'], '@parlementaire?slug='.$t[0]['slug']); ?></td>
	<?php for($i = 1 ; $i < count($t) ; $i++) { ?><td<?php echo $t[$i]['style']; ?> class="<?php echo $class[$ktop[$i]]; ?>">
	<?php 
     if (preg_match('/\./', $t[$i]['value'])) {
       printf('%02d', $t[$i]['value']);
     } else{
       echo $t[$i]['value']; 
     }
	 ?>
	 </td>
  <?php } ?>
  </tr>
<?php } ?>
</table>
</div>
<div>
<h3>Explications :</h3>
<ul>
  <li><strong>Semaines d'activité</strong> : Nombre de semaines où le député a été relevé présent en commission ou a pris la parole (même brièvement) en hémicycle</li>
  <li><strong>Commission séances</strong> : Nombre de séances de commission où le député a été relevé présent</li>
  <li><strong>Commission interventions</strong> : Nombre d'interventions prononcées par le député en commissions</li>
  <li><strong>Hémicycle Interventions longues</strong> : Nombre d'interventions de plus de 20 mots prononcées par le député en hémicycle</li>
  <li><strong>Hémicycle Interventions courtes</strong> : Nombre d'interventions de 20 mots et moins prononcées par le député en hémicycle</li>
  <li><strong>Amendements signés</strong> : Nombre d'amendements signés ou co-signés par le député</li>
  <li><strong>Amendements adoptés</strong> : Nombre d'amendements adoptés qui ont été signés ou cosignés par le député</li>
  <li><strong>Amendements rejetés</strong> : Nombre d'amendements rejetés qui ont été signés ou cosignés par le député</li>
  <li><strong>Questions écrites</strong> : Nombre de questions écrites soumises par le député</li>
  <li><strong>Questions orales</strong> : Nombre de questions orales posées par le député</li>
</ul>
</div>
</div>
</div>
