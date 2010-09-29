<h1>Synthèse générale de l'activité parlemmentaire<br/><small>(sur les 12 derniers mois)</small></h1>
<h2>Activité de tous les députés ayant au moins 6 mois de mandat :</h2>
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
	       'semaines_presence' => 'w',
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
$bulles = array("Députés",
               "Semaines d'activité -- Nombre de semaines où le député a été relevé présent -- en commission ou a pris la parole (même brièvement) en hémicycle</li>",
               "Séances de Commission -- Nombre de séances de commission où le député a été relevé présent",
               "Interventions en Commission -- Nombre d'interventions prononcées par le député en commissions",
               "Interventions longues en Hémicycle -- Nombre d'interventions de plus de 20 mots prononcées par le député en hémicycle",
               "interventions courtes en Hémicycle -- Nombre d'interventions de 20 mots et moins prononcées par le député en hémicycle",
               "Amendements signés -- Nombre d'amendements signés ou co-signés par le député",
               "Amendements adoptés -- Nombre d'amendements du député qui ont été adoptés en séance",
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
    <th title="Députés" class="jstitle <?php echo $class['parl']; ?>">&nbsp;</th>
    <th title="Semaines d'activité -- Nombre de semaines où le député a été relevé présent en commission ou a pris la parole (même brièvement) en hémicycle" class="jstitle <?php if ($sort == 1) echo 'tr_odd';?>"><?php echo link_to('Semaines', $top_link.'sort=1'); ?></th>
    <th title="Commission -- Nombre de séances de commission auxquelles le député a participé" colspan="2" class="jstitle <?php if ($sort == 2 || $sort == 3) echo 'tr_odd';?>">Commission</th>
    <th title="Hémicycle -- Nombre d'interventions du député en séances d'hémicycle" colspan="2" class="jstitle <?php if ($sort == 4 || $sort == 5) echo 'tr_odd';?>">Hémicycle</th>
    <th title="Amendements -- Nombre d'amendements signés par le député" colspan="2" class="jstitle <?php if ($sort == 6 || $sort == 7) echo 'tr_odd';?>">Amendements</th>
    <th title="Rapports -- Nombre de rapports ou avis dont le député est l'auteur"class="jstitle <?php if ($sort == 8) echo 'tr_odd';?>"><?php echo link_to('Rapports', $top_link.'sort=8'); ?></th>
    <th title="Nombre de propositions de loi ou de résolution dont le député est signataire" colspan="2" class="jstitle <?php if ($sort == 9 || $sort == 10) echo 'tr_odd';?>">Propositions</th>
    <th title="Nombre de questions au gouvernement formulées par le député" colspan="2" class="jstitle <?php if ($sort == 11 || $sort == 12) echo 'tr_odd';?>">Questions</th>
  </tr>
  <tr>
    <th class="<?php echo $class['parl']; ?>">&nbsp;</th><?php
    $last = end($tops); $i = 0; 
    foreach($ktop as $key) { 
      $i++ ; 
    ?>
    <th title="<?php echo $bulles[$i]; ?>" class="jstitle <?php echo $class[$key]; if ($sort == $i) echo ' tr_odd'?>"><?php echo link_to($title[$key], $top_link.'sort='.$i); ?></th>
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
    <td title="<?php echo $t[0]['nom']; ?> -- Député<?php if ($t[0]['sexe'] === "F") echo 'e'; ?> <?php echo $t[0]['groupe_acronyme'].' '.preg_replace('/([^\'])$/', '\\1 ', Parlementaire::$dptmt_pref[trim($t[0]['nom_circo'])]).$t[0]['nom_circo']; ?>" class="jstitle couleur_<?php echo strtolower($t[0]['groupe_acronyme']); ?> <?php echo $class['parl']; ?>"><a name="<?php echo $t[0]['slug']; ?>" href="<?php echo url_for('@parlementaire?slug='.$t[0]['slug']); ?>"></a><?php echo link_to($t[0]['nom'], '@parlementaire?slug='.$t[0]['slug']); ?></td>
    <?php for($i = 1 ; $i < count($t) ; $i++) { ?>
      <td title="<?php echo $t[$i]['value'].' '; if ($t[$i]['value'] < 2) echo preg_replace('/s (.*-- )/', ' \\1', preg_replace('/s (.*-- )/', ' \\1', $bulles[$i])); else echo $bulles[$i]; ?>" <?php echo $t[$i]['style']; ?> class="jstitle <?php echo $class[$ktop[$i]]; ?>">
      <?php if (preg_match('/\./', $t[$i]['value']))
        printf('%02d', $t[$i]['value']);
      else echo $t[$i]['value']; ?>
      </td>
    <?php } ?>
  </tr>
<?php } ?>
</table>
</div>
</div>
</div>
<div class="synthese_div">
<h2>Répartition de l'activité des députés sur les 12 derniers mois par groupe politique :</h2>
<div class="aligncenter"><?php echo include_component('plot', 'newGroupes', array('type' => 'all')); ?></div>
</div>
<div class="synthese_div">
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
<p><a href="<?php echo url_for('@faq'); ?>">Lire notre FAQ pour plus d'explications</a></p>
</div>
