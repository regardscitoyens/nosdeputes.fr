<?php $fin = myTools::isFinLegislature();
foreach ($tops as $t) if (!isset($date)) {$date = $t[0]['updated_at']; break;} ?>
<h1>Synthèse générale de l'activité parlementaire<br/><small><?php if ($fin) echo "sur toute la législature"; else echo "sur les 12 derniers mois"; ?></small></h1>
<h2 class="aligncenter"><small>(dernière <a href="<?php echo url_for('@faq'); ?>#post_2">mise-à-jour</a> le <?php echo preg_replace('/20(\d+)-(\d+)-(\d+) (\d+):(\d+):\d+/', '$3/$2/$1 à $4H$5', $date); ?>)</h2>
<h2>Activité <?php if ($fin) echo "mensuelle moyenne "; ?>de tous les députés<?php if (!$fin) echo " ayant au moins 10 mois de mandat"; ?> :</h2>
<?php 
$sf_response->setTitle('Synthèse générale des députés');
$title = array('semaines_presence' => 'd\'activité',
	       'commission_presences' => 'séances',
	       'commission_interventions'=> 'interv.',
	       'hemicycle_interventions'=>'interv.<br/>longues',
	       'hemicycle_interventions_courtes'=>'interv.<br/>courtes',
	       'amendements_signes' => 'signés',
	       'amendements_adoptes'=>'adoptés',
	       'rapports' => 'écrits',
               'propositions_ecrites' => 'écrites',
               'propositions_signees' => 'signées',
	       'questions_ecrites' => 'écrites',
	       'questions_orales' => 'orales');
$class = array('parl' => 'p',
	       'semaines_presence' => 'we',
	       'commission_presences' => 'cp',
	       'commission_interventions'=> 'ci',
	       'hemicycle_interventions'=>'hl',
	       'hemicycle_interventions_courtes'=>'hc',
	       'amendements_signes' => 'as',
	       'amendements_adoptes'=>'aa',
               'rapports' => 'ra',
               'propositions_ecrites' => 'pe',
               'propositions_signees' => 'ps',
	       // 'amendements_rejetes' => 'ar',
	       'questions_ecrites' => 'qe',
	       'questions_orales' => 'qo');
$bulles = array("",
               "Semaines d'activité -- Nombre de semaines où le député a été relevé présent -- en commission ou a pris la parole (même brièvement) en hémicycle",
               "Séances de Commission -- Nombre de séances de commission où le député a été relevé présent",
               "Interventions en Commission -- Nombre d'interventions prononcées par le député en commissions",
               "Interventions longues en Hémicycle -- Nombre d'interventions de plus de 20 mots prononcées par le député en hémicycle",
               "interventions courtes en Hémicycle -- Nombre d'interventions de 20 mots et moins prononcées par le député en hémicycle",
               "Amendements signés -- Nombre d'amendements signés ou co-signés par le député",
               "Amendements adoptés -- Nombre d'amendements signés par le député qui ont été adoptés en séance",
               "Rapports écrits -- Nombre de rapports ou avis dont le député est l'auteur",
               "Propositions écrites -- Nombre de propositions de loi ou de résolution dont le député est l'auteur",
               "Propositions signées -- Nombre de propositions de loi ou de résolution dont le député est cosignataire",
               "Questions écrites -- Nombre de questions écrites soumises par le député",
               "Questions orales -- Nombre de questions orales posées par le député");
?>
<div class="liste_deputes_top">
<div class="synthese">
<table>
  <tr>
    <th class="<?php echo $class['parl']; ?>">&nbsp;</th>
    <?php if ($nb_mdts) echo '<th>Nb mandats</th>'; ?>
    <th title="Trier par : Semaines d'activité -- Nombre de semaines où le député a été relevé présent -- en commission ou a pris la parole (même brièvement) en hémicycle" class="jstitle <?php if ($sort == 1) echo 'tr_odd';?>"><?php echo link_to('Semaines', $top_link.'sort=1'); ?></th>
    <th colspan="2" class="<?php if ($sort == 2 || $sort == 3) echo 'tr_odd';?>">Commission</th>
    <th colspan="2" class="<?php if ($sort == 4 || $sort == 5) echo 'tr_odd';?>">Hémicycle</th>
    <th colspan="2" class="<?php if ($sort == 6 || $sort == 7) echo 'tr_odd';?>">Amendements</th>
    <th title="Trier par : Rapports écrits -- Nombre de rapports ou avis dont le député est l'auteur" class="jstitle <?php if ($sort == 8) echo 'tr_odd';?>"><?php echo link_to('Rapports', $top_link.'sort=8'); ?></th>
    <th colspan="2" class="<?php if ($sort == 9 || $sort == 10) echo 'tr_odd';?>">Propositions</th>
    <th colspan="2" class="<?php if ($sort == 11 || $sort == 12) echo 'tr_odd';?>">Questions</th>
    <th style="width:10px;"/>
  </tr>
  <tr>
    <th title="Trier par : Nom de famille" class="jstitle <?php echo $class['parl']; ?>"><?php echo link_to('Nom', '@top_global'); ?></th><?php
    $last = end($tops); $i = 0; 
    foreach($ktop as $key) { 
      $i++ ; 
    ?>
    <th title="<?php echo "Trier par : ".$bulles[$i]; ?>" class="jstitle <?php echo $class[$key]; if ($sort == $i) echo ' tr_odd'?>"><?php echo link_to($title[$key], $top_link.'sort='.$i); ?></th>
	<?php 
	} ?>
    <th style="width:10px;"/>
  </tr>
</table>
<?php array_unshift($ktop, ''); ?>
<div class="tableau_synthese">
<table>
  <?php 
  $cpt = 0; 
  foreach($tops as $t) {
    $cpt++;?><tr<?php if ($cpt %2) echo ' class="tr_odd"'?>>
    <td id="<?php echo $t[0]['slug']; ?>" class="jstitle phototitle c_<?php echo strtolower($t[0]['groupe_acronyme']); ?> <?php echo $class['parl']; ?>" title="<?php echo $t[0]['nom']; ?> -- Député<?php if ($t[0]['sexe'] === "F") echo 'e'; ?> <?php echo $t[0]['groupe_acronyme'].' '.preg_replace('/([^\'])$/', '\\1 ', Parlementaire::$dptmt_pref[trim($t[0]['nom_circo'])]).$t[0]['nom_circo']; ?>"><a class="urlphoto" href="<?php echo url_for('@parlementaire?slug='.$t[0]['slug']); ?>"><?php echo $t[0]['nom']; ?></a></td>
    <?php if ($nb_mdts) echo '<td>'.count(unserialize($t[0]['autres_mandats'])).'</td>';
    $field = "value";
    if ($fin) 
      $field = "moyenne";
    for($i = 1 ; $i < count($t) ; $i++) {
      echo '<td title="'.$t[$i]['value'].' ';
      $leg = $bulles[$i];
      if ($t[$i]['value'] < 2)
        $leg = preg_replace('/s (.*-- )/', ' \\1', preg_replace('/s (.*-- )/', ' \\1', $leg));
      if ($fin)
        $leg = str_replace(" -- Nombre", " sur ".$t[0]["nb_mois"]." mois de mandat -- Nombre", $leg);
      echo $leg;
      echo '" '.$t[$i]['style'].' class="jstitle '.$class[$ktop[$i]].'">';
      if (!$fin && preg_match('/\./', $t[$i]['value']))
        printf('%02d', $t[$i]['value']);
      else echo str_replace(".", ",", ($fin ? sprintf('%.02f', $t[$i][$field]) : $t[$i][$field]));
      echo '</td>';
    } ?>
  </tr>
<?php } ?>
</table>
</div>
<p class="aligncenter"><small>Les chiffres en couleur indiquent que le député se trouve pour le critère indiqué parmi <span style="color:green">les 150 premiers</span> ou <span style="color:red">les 150 derniers</span>.</small></p>
</div></div>
<h2 id="groupes">Activité moyenne d'un député de chaque groupe politique <?php if ($fin) echo "sur toute la législature"; else echo "au cours des 12 derniers mois"; ?> :</h2>
<div class="liste_deputes_top">
<div class="synthese">
<table>
  <tr>
    <th class="<?php echo $class['parl']; ?>">&nbsp;</th>
    <th title="Semaines d'activité -- Nombre moyen de semaines où un député de ce groupe -- a été relevé présent en commission ou a pris la parole (même brièvement) en hémicycle" class="jstitle <?php if ($sort == 1) echo 'tr_odd';?>">Semaines</th>
    <th colspan="2" class="<?php if ($sort == 2 || $sort == 3) echo 'tr_odd';?>">Commission</th>
    <th colspan="2" class="<?php if ($sort == 4 || $sort == 5) echo 'tr_odd';?>">Hémicycle</th>
    <th colspan="2" class="<?php if ($sort == 6 || $sort == 7) echo 'tr_odd';?>">Amendements</th>
    <th title="Rapports écrits -- Nombre moyen de rapports ou avis dont le député est l'auteur" class="jstitle <?php if ($sort == 8) echo 'tr_odd';?>">Rapports</th>
    <th colspan="2" class="<?php if ($sort == 9 || $sort == 10) echo 'tr_odd';?>">Propositions</th>
    <th colspan="2" class="<?php if ($sort == 11 || $sort == 12) echo 'tr_odd';?>">Questions</th>
    <th style="width:10px;"/>
  </tr>
  <tr>
    <th class="jstitle <?php echo $class['parl']; ?>">Groupe</th>
    <?php $i = 1;
    foreach($ktop as $key) {
      if ($key === "") continue;
      $bulles[$i] = str_replace('Nombre', 'Nombre moyen', str_replace('le député', 'un député de ce groupe', $bulles[$i]));
      echo '<th title="'.$bulles[$i].'" class="jstitle '.$class[$key].($sort == $i ? ' tr_odd' : '').'">'.$title[$key].'</th>';
      $i++;
    } ?>
    <th style="width:10px;"/>
  </tr>
</table>
<div class="synthese_groupes">
<table>
  <?php $cp = 0;
  $cp = myTools::echo_synthese_groupe($gpes, $bulles, $class, $ktop, $cp);
  if ($nb_mdts) {
    $cp = myTools::echo_synthese_groupe($sexes, $bulles, $class, $ktop, $cp);
    $cp = myTools::echo_synthese_groupe($mandats, $bulles, $class, $ktop, $cp);
  } ?>
</table>
</div>
</div>
</div>
<div class="synthese_div">
<h2>Répartition de l'activité des députés sur <?php if ($fin) echo "toute la législature"; else echo "les 12 derniers mois"; ?> par groupe politique :</h2>
<div class="aligncenter"><?php echo include_component('plot', 'newGroupes', array('type' => 'all')); ?></div>
</div>
<div id="legende" class="synthese_div">
<h2>Explications :</h2>
<ul>
  <li><strong>Semaines d'activité</strong> : Nombre de semaines où le député a été relevé présent en commission ou a pris la parole (même brièvement) en hémicycle</li>
  <li><strong>Commission séances</strong> : Nombre de séances de commission où le député a été relevé présent</li>
  <li><strong>Commission interventions</strong> : Nombre d'interventions prononcées par le député en commissions</li>
  <li><strong>Hémicycle interventions longues</strong> : Nombre d'interventions de plus de 20 mots prononcées par le député en hémicycle</li>
  <li><strong>Hémicycle interventions courtes</strong> : Nombre d'interventions de 20 mots et moins prononcées par le député en hémicycle</li>
  <li><strong>Amendements signés</strong> : Nombre d'amendements signés ou co-signés par le député</li>
  <li><strong>Amendements adoptés</strong> : Nombre d'amendements adoptés qui ont été signés ou cosignés par le député</li>
  <li><strong>Rapports écrits</strong> : Nombre de rapports ou avis dont le député est l'auteur</li>
  <li><strong>Propositions écrites</strong> : Nombre de propositions de loi ou de résolution dont le député est l'auteur</li>
  <li><strong>Propositions signées</strong> : Nombre de propositions de loi ou de résolution dont le député est cosignataire</li>
  <li><strong>Questions écrites</strong> : Nombre de questions au gouvernement écrites soumises par le député</li>
  <li><strong>Questions orales</strong> : Nombre de questions au gouvernement orales posées par le député</li>
</ul>
<p><a href="<?php echo url_for('@faq#post_1'); ?>">Lire notre FAQ pour plus d'explications</a></p>
</div>
