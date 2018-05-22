<?php
$abs = '';
$serv = '';
if (isset($absolute) && $absolute) {
  $abs = 'absolute=true';
  $serv = myTools::getProtocol().'://'.$_SERVER['SERVER_NAME'];
}
if (!isset($target))
  $target = '';

$titres = array(
  'semaines_presence'               => 'Semaines d\'activité',
  'commission_presences'            => 'Présences en commission',
  'commission_interventions'        => 'Interventions en commission',
  'hemicycle_presences'             => 'Présences en hémicycle : Information non publique',
  'hemicycle_interventions'         => 'Interventions longues en hémicycle',
//'hemicycle_interventions_courtes' => 'Interventions courtes en hémicycle',
  'amendements_proposes'            => 'Amendements proposés',
//'amendements_adoptes'             => 'Amendements adoptés',
//'amendements_rejetes'             => 'Amendements rejetés',
  'rapports'                        => 'Rapports écrits',
  'propositions_ecrites'            => 'Propositions de loi écrites',
  'propositions_signees'            => 'Propositions de loi signées',
  'questions_ecrites'               => 'Questions écrites',
  'questions_orales'                => 'Questions orales',
);
$images = array(
  'semaines_presence'               => 'ico_sem_%s.png',
  'commission_presences'            => 'ico_com_pre_%s.png',
  'commission_interventions'        => 'ico_com_inter_%s.png',
  'hemicycle_presences'             => 'ico_hem_pre_gris.png',
  'hemicycle_interventions'         => 'ico_inter_hem_long_%s.png',
//'hemicycle_interventions_courtes' => 'ico_inter_hem_court_%s.png',
  'amendements_proposes'            => 'ico_amendmt_sign_%s.png',
//'amendements_adoptes'             => 'ico_amendmt_ado_%s.png',
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
  'amendements_proposes'            => '6',
  'amendements_signes'              => '7',
  'amendements_adoptes'             => '8',
  'rapports'                        => '9',
  'propositions_ecrites'            => '10',
  'propositions_signees'            => '11',
  'questions_ecrites'               => '12',
  'questions_orales'                => '13'
);
$couleur2style = array(
  'vert'  => ' style="color: green;font-weight:bold;"',
  'gris'  => ' style="font-weight:bold;"',
  'rouge' => ' style="color: red;font-style:italic;"'
);
$top = $parlementaire->getTop();
if (!$top)
  return ;

$details = "";
$title = "";
$rank = true;
if (myTools::isFinLegislature()) {
  $details = "totale ";
  $duree = $top["nb_mois"]." mois";
  $title = "Bilan d'activité totale -- durant ".$top["nb_mois"]." mois d'exercice du député, -- vacances parlementaires exclues";
  $rank = ($top["nb_mois"] >= 6);
} else if ($parlementaire->isEnMandat()) {
  $mois = min(12, $parlementaire->getNbMois());
  if ($mois < 12) {
    if ($mois <= 1) $duree = 'premier';
    else if ($mois < 10) $duree = $mois.' premiers';
    else $duree = $mois;
    $rank = ($mois >= 10 || myTools::isFreshLegislature());
  } else {
    $duree = "12 derniers";
  }
  $duree .= " mois";
} else {
  $details = "totale ";
  $title = "Bilan d'activité totale -- durant ".$parlementaire->getNbMois()." mois d'exercice du député, -- vacances parlementaires exclues";
  $rank = false;
  $weeks = (strtotime($parlementaire->fin_mandat) - strtotime($parlementaire->debut_mandat))/(60*60*24*7);
  if ($weeks > 52) $duree = sprintf('%d mois', $weeks/4.33);
  else $duree = sprintf('%d semaine%s', $weeks, ($weeks >= 2 ? "s" : ""));
}
?>
<h3<?php if ($title) echo ' class="jstitle" title="'.$title.'"'; ?>>Activité <?php echo $details; ?><small>(<?php echo $duree; ?>)</small> :</h3>
<ul><?php
$icosize = 16;
if (isset($widthrate))
  $icosize = floor($icosize*$widthrate);
foreach(array_keys($images) as $k) {
  if ($k === "hemicycle_presences") {
    echo '<li'.$couleur2style['gris'].'><a'.$target.' href="'.url_for('@faq', $abs).'#post_4" class="jstitle" title="'.$titres[$k].'"><img style="height: '.$icosize.'px; width: '.$icosize.'px;" src="'.$serv.$sf_request->getRelativeUrlRoot().'/images/xneth/'.$images[$k].'" alt="'.$titres[$k].'" /> : ??</a></li>';
  } else {
    $value = (isset($top[$k]['value']) ? $top[$k]['value'] : 0);
    $couleur = 'gris';
    $titre = $value.' '.$titres[$k];
    if ($value < 2)
      $titre = preg_replace('/s$/', '', str_replace('s ', ' ', $titre));
    if ($rank && $top[$k]['rank'] <= 150 && $value) {
      $couleur = 'vert';
      $titre .=' (fait partie des 150 plus actifs sur ce critère)';
    }
    else if ($rank && $top[$k]['rank'] >= $top[$k]['max_rank'] - 150) {
      $couleur = 'rouge';
      $titre .= ' (fait partie des 150 moins actifs sur ce critère)';
    }
    echo '<li'.$couleur2style[$couleur].'>';
    echo '<'.($rank ? 'a' : 'span').$target.' class="jstitle" title="'.$titre.'" href="'.url_for('@top_global_sorted?sort='.$sort[$k].'#'.$parlementaire->slug, $abs).'">';
    echo '<img style="height: '.$icosize.'px; width: '.$icosize.'px;" src="'.$serv.$sf_request->getRelativeUrlRoot().'/images/xneth/';
    printf($images[$k], $couleur);
    echo '" alt="'.$titre.'" /> : '.$value.'</'.($rank ? 'a' : 'span').'></li>';
  }
}?></ul>
