<?php
$indicateurs = myTools::$indicateurs;
$fields = array(
  'semaines_presence',
  'commission_presences',
  'commission_interventions',
  'hemicycle_presences',
  'hemicycle_interventions',
  'amendements_signes',
  'amendements_adoptes',
  'rapports',
  'propositions_ecrites',
  'propositions_signees',
  'questions_ecrites',
  'questions_orales'
);
$images = array(
  'semaines_presence'               => 'ico_sem_%s.png',
  'commission_presences'            => 'ico_com_pre_%s.png',
  'commission_interventions'        => 'ico_com_inter_%s.png',
  'hemicycle_presences'             => 'ico_hem_pre_gris.png',
  'hemicycle_interventions'         => 'ico_inter_hem_long_%s.png',
//'hemicycle_interventions_courtes' => 'ico_inter_hem_court_%s.png',
  'amendements_signes'              => 'ico_amendmt_sign_%s.png',
  'amendements_adoptes'             => 'ico_amendmt_ado_%s.png',
//'amendements_rejetes'             => 'ico_amendmt_ref_%s.png',
  'rapports'                        => 'ico_rap_%s.png',
  'propositions_ecrites'            => 'ico_pple_%s.png',
  'propositions_signees'            => 'ico_ppls_%s.png',
  'questions_ecrites'               => 'ico_quest_ecrit_%s.png',
  'questions_orales'                => 'ico_quest_oral_%s.png'
);
$sort = array(
  'semaines_presence'               => '1',
  'commission_presences'            => '2',
  'commission_interventions'        => '3',
  'hemicycle_interventions'         => '4',
  'hemicycle_interventions_courtes' => '5',
  'amendements_signes'              => '6',
  'amendements_adoptes'             => '7',
  'rapports'                        => '8',
  'propositions_ecrites'            => '9',
  'propositions_signees'            => '10',
  'questions_ecrites'               => '11',
  'questions_orales'                => '12'
);
$couleur2style = array(
  'vert'  => ' style="color: green;font-weight:bold;"',
  'gris'  => ' style="font-weight:bold;"',
  'rouge' => ' style="color: red;font-style:italic;"'
);
$top = $parlementaire->getTop();
if (!$top)
  return ;
if (!$parlementaire->fin_mandat || $parlementaire->fin_mandat < $parlementaire->debut_mandat) {
  $rank = 0;
  $headstr = "premier";
  if (myTools::isDebutMandature() && strtotime(myTools::getDebutMandature()) > strtotime($parlementaire->debut_mandat)) {
    $stdate = myTools::getDebutMandature();
    if (strtotime(myTools::getDebutMandature()) - strtotime($parlementaire->debut_mandat) > 2160000)
      $headstr = "dernier";
    $rank = 1;
  }
  else $stdate = $parlementaire->debut_mandat;
  $mois = floor((myTools::getEndDataTime() - strtotime($stdate) ) / (60*60*24*30));
  if($mois < 12) {
    echo '<h3>Activité <small>(';
    if ($mois <= 1) echo $headstr;
    else if ($mois < 10) echo $mois.' '.$headstr.'s';
    else echo $mois.' '.$headstr.'s';
    echo ' mois de mandat)</small> :</h3>';
    if ($mois < 4) $rank = 0;
  }else {
    echo '<h3>Activité <small>('.myTools::getTextEndData().')</small> :</h3>';
    $rank = 1;
  }
 } else {
  $rank = 0;
  $weeks = (strtotime($parlementaire->fin_mandat) - strtotime(max(myTools::getDebutData(), $parlementaire->debut_mandat)))/(60*60*24*7);
  if ($weeks > 52) $temps = sprintf('%d mois', $weeks/4.33);
  else $temps = sprintf('%d semaines', $weeks);
  echo '<h3>Activité sur '.$temps.' :</h3>';
 }
?>
<ul><?php
$icosize = 16;
foreach($fields as $k) {
  if ($k === "hemicycle_presences") {
    echo '<li'.$couleur2style['gris'].'><a href="'.url_for('@faq').'#post_5" class="jstitle" title="'.$indicateurs[$k]['titre'].' --  -- '.$indicateurs[$k]['desc'].'"><img style="height: '.$icosize.'px; width: '.$icosize.'px;" src="/images/xneth/'.$images[$k].'" alt="'.$indicateurs[$k]['titre'].'" /> : ??</a></li>';
  } else {
    $value = (isset($top[$k]['value']) ? $top[$k]['value'] : 0);
    $couleur = 'gris';
    $titre = $value.' '.$indicateurs[$k]['titre'];
    if ($value < 2)
      $titre = preg_replace('/s$/', '', str_replace('s ', ' ', $titre));
    if ($rank && $top[$k]['rank'] <= 100 && $value) {
      $couleur = 'vert';
      $titre .=' (fait partie des 100 plus actifs sur ce critère)';
    }
    else if ($rank && $top[$k]['rank'] >= $top[$k]['max_rank'] - 100) {
      $couleur = 'rouge';
      $titre .= ' (fait partie des 100 moins actifs sur ce critère)';
    }
    $titre .= ' --  -- '.$indicateurs[$k]['desc'];
    echo '<li'.$couleur2style[$couleur].'>';
    echo '<'.($rank ? 'a' : 'span').' class="jstitle" title="'.$titre.'" href="'.url_for('@top_global_sorted?sort='.$sort[$k]).'#'.$parlementaire->slug.'">';
    echo '<img style="height: '.$icosize.'px; width: '.$icosize.'px;" src="/images/xneth/';
    printf($images[$k], $couleur);
    echo '" alt="'.$titre.'" /> : '.$value.'</'.($rank ? 'a' : 'span').'></li>';
  }
}?></ul>
