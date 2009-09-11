<?php
$title = array('semaine' => 'Semaines en commission',
	       'commission_presences' => 'Présences en commission',
	       'commission_interventions'=> 'Interventions en commission',
	       'hemicycle_interventions'=>'Interventions longues en hémicycle',
	       'hemicycle_invectives'=>'Interventions courtes en hémicycle',
	       'amendements_signes' => 'Amendements signés',
	       'amendements_adoptes'=>'Amendements adoptés',
	       'amendements_rejetes' => 'Amendements rejetés',
	       'questions_ecrites' => 'Questions écrites',
	       'questions_orales' => 'Questions orales');
      $top = $parlementaire->getTop();
if (!$top)
  return ;
if (!$parlementaire->fin_mandat) {
  echo '<h3>Le meilleur et le pire (sur 12 derniers mois)</h3>';
  $rank = 1;
 } else {
  $rank = 0;
  $weeks = (strtotime($parlementaire->fin_mandat) - strtotime($parlementaire->debut_mandat))/(60*60*24*7);
  echo '<h3>Bilan de ses '.$weeks.' semaines de mandat </h3>';
 }
?>
<ul><?php
foreach(array_keys($top) as $k) {
  echo '<li style="';
  if ($rank && $top[$k]['rank'] <= 150) 
    echo 'color: green';
  if ($rank && $top[$k]['rank'] >= $top[$k]['max_rank'] - 150) 
    echo 'color: red';
  echo '">';
  echo $title[$k].' : '.$top[$k]['value'];
  if ($rank ){
    echo ' ('.$top[$k]['rank'].'<sup>';
    if ($top[$k]['rank'] == 1) {
      if($parlementaire->sexe == 'H')
        echo 'er';
      else
        echo 're';
    }
    else
        echo 'e';
    echo '</sup> sur '.$top[$k]['max_rank'].')';
  }
  echo '</li>';
}?></ul>

