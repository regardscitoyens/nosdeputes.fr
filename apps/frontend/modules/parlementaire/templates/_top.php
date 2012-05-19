<?php
$abs = '';
$serv = '';
if (isset($absolute) && $absolute) {
  $abs = 'absolute=true';
  $serv = 'http://'.$_SERVER['SERVER_NAME'];
}
$titres = array('semaines_presence' => 'Semaines d\'activité',
	       'commission_presences' => 'Présences en commission',
	       'commission_interventions'=> 'Interventions en commission',
	       'hemicycle_interventions'=>'Interventions longues en hémicycle',
	        //             'hemicycle_interventions_courtes'=>'Interventions courtes en hémicycle',
	       'amendements_signes' => 'Amendements signés',
		//	       'amendements_adoptes'=>'Amendements adoptés',
		//	       'amendements_rejetes' => 'Amendements rejetés',
               'rapports' => 'Rapports écrits',
               'propositions_ecrites' => 'Propositions de loi écrites',
               'propositions_signees' => 'Propositions de loi signées',
	       'questions_ecrites' => 'Questions écrites',
	       'questions_orales' => 'Questions orales',
);
$images = array('semaines_presence' => 'ico_sem_%s.png',
	       'commission_presences' => 'ico_com_pre_%s.png',
	       'commission_interventions'=> 'ico_com_inter_%s.png',
	       'hemicycle_interventions'=>'ico_inter_hem_long_%s.png',
		//	       'hemicycle_interventions_courtes'=>'ico_inter_hem_court_%s.png',
	       'amendements_signes' => 'ico_amendmt_sign_%s.png',
		//	       'amendements_adoptes'=>'ico_amendmt_ado_%s.png',
		//	       'amendements_rejetes' => 'ico_amendmt_ref_%s.png',
               'rapports' => 'ico_rap_%s.png',
               'propositions_ecrites' => 'ico_pple_%s.png',
               'propositions_signees' => 'ico_ppls_%s.png',
	       'questions_ecrites' => 'ico_quest_ecrit_%s.png',
	       'questions_orales' => 'ico_quest_oral_%s.png');
$sort = array('semaines_presence' => '1',
	       'commission_presences' => '2',
	       'commission_interventions'=> '3',
	       'hemicycle_interventions'=>'4',
	       'hemicycle_interventions_courtes'=>'5',
	       'amendements_signes' => '6',
	       'amendements_adoptes'=>'7',
	       'rapports' => '8',
 	       'propositions_ecrites' => '9',
               'propositions_signees' => '10',
	       'questions_ecrites' => '11',
	       'questions_orales' => '12');
$couleur2style = array('vert' => ' style="color: green"',
	       'gris' => '',
	       'rouge' => ' style="color: red"');
$top = $parlementaire->getTop();
if (!$top)
  return ;
if (myTools::isFinLegislature()) {
  echo '<h3 class="jstitle" title="Bilan d\'activité totale -- durant '.$top["nb_mois"].' mois de mandat(s) du député, -- vacances parlementaires exclues">Activité totale ('.$top["nb_mois"].' mois) :</h3>';
  $rank = 1;
} else {
 if (!$parlementaire->fin_mandat || $parlementaire->fin_mandat < $parlementaire->debut_mandat) {
  $mois = floor((time() - strtotime($parlementaire->debut_mandat) ) / (60*60*24*30));
  if($mois < 12) {
    echo '<h3>Activité <small>(';
    if ($mois <= 1) echo 'premier';
    else if ($mois < 10) echo $mois.' premiers';
    else echo $mois;
    echo ' mois de mandat)</small> :</h3>';
    $rank = 0;
  }else {
    echo '<h3>Activité <small>(12 derniers mois)</small> :</h3>';
    $rank = 1;
  }
 } else {
  $rank = 0;
  $weeks = (strtotime($parlementaire->fin_mandat) - strtotime($parlementaire->debut_mandat))/(60*60*24*7);
  if ($weeks > 52) $temps = sprintf('%d mois', $weeks/4.33);
  else $temps = sprintf('%d semaines', $weeks);
  echo '<h3>Activité sur '.$temps.' :</h3>';
 }
}
?>
<ul><?php
$icosize = 16;
if (isset($widthrate))
  $icosize = floor($icosize*$widthrate);
foreach(array_keys($images) as $k) {
  if (isset($top[$k]['value']))
    $value = $top[$k]['value'];
  else
    $value = 0;
  $couleur = 'gris';
  $titre = $value.' '.$titres[$k];
  if ($value < 2) $titre = preg_replace('/s$/', '', str_replace('s ', ' ', $titre));
  if ($rank && $top[$k]['rank'] <= 150) {
    $couleur = 'vert';
    $titre .=' (fait partie des 150 premiers)';
  }
  else if ($rank && $top[$k]['rank'] >= $top[$k]['max_rank'] - 150) {
    $couleur = 'rouge';
    $titre .= ' (fait partie des 150 derniers)';
  }
  echo '<li';
  echo $couleur2style[$couleur];
  echo'><';
  if ($rank)
    echo 'a';
  else echo 'span';
  echo ' class="jstitle" title="'.$titre.'" href="'.url_for('@top_global_sorted?sort='.$sort[$k].'#'.$parlementaire->slug, $abs).'"><img style="height: '.$icosize.'px; width: '.$icosize.'px;" src="'.$serv.$sf_request->getRelativeUrlRoot().'/images/xneth/';
  printf($images[$k], $couleur);
  echo '" alt="'.$titre.'" />';
  echo ' : '.$value.'</';
  if ($rank)
    echo 'a';
  else echo 'span';
  echo '></li>';
}?></ul>
