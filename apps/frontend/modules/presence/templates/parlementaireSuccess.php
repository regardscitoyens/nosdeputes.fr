<?php $titre = "Présence en ";
if ($type == "all") $titre .= "hémicycle et commissions";
else $titre .= $type;
$sf_response->setTitle($titre." de ".$parlementaire->nom);
echo include_component("parlementaire", "header", array("parlementaire" => $parlementaire, "titre" => $titre)); ?>
<h3><?php $n_presences = count($presences); if ($n_presences == 0) echo "Aucune"; else echo $n_presences; echo " présence"; if ($n_presences > 1) echo "s"; if ($type == "commission") echo " enregistrée"; else echo " relevée"; if ($n_presences > 1) echo "s"; ?></h3>
<?php if ($n_presences > 0) {
  echo '<ul>';
  $seance0 = $presences[0]->getSeance();
  $date0 = $seance0->date;
  if (preg_match('/(\d{4})-(\d{2})-(\d{2})/', $date0, $match))
    $month0 = 'date_'.$match[1].'_'.$match[2];
  else $month0 = "";
  echo '<li id="'.$month0.'"><h4>'.myTools::displayDateSemaine($date0).'</h4><ul>';
  foreach($presences as $presence) {
    $seance = $presence->getSeance();
    $date = $seance->date;
    if ($date0 != $date) {
      echo '</ul></li><li';
      if (preg_match('/(\d{4})-(\d{2})-(\d{2})/', $date, $match) && $month0 != 'date_'.$match[1].'_'.$match[2]) {
        $month0 = 'date_'.$match[1].'_'.$match[2];
        echo ' id="'.$month0.'"';
      }
      $date0 = $date;
      echo "><h4>".myTools::displayDateSemaine($date)."</h4><ul>";
    }
    echo '<li><a href="'.url_for('@interventions_seance?seance='.$seance->id).'">';
    if ($type != "hemicycle") {
      if ($seance->type == "commission" && $o = $seance->getOrganisme())
        echo $o->getNom();
      else if ($type != "hemicycle") echo "Hémicycle";
      echo "&nbsp;&mdash;&nbsp;";
    }
    echo $seance->getTitre().'</a>';
    $nbpreuves = $presence->getNbPreuves();
    if ($nbpreuves != 0) {
      if ($nbpreuves > 1) $preuves_str = $nbpreuves."&nbsp;preuves";
      else $preuves_str = "1&nbsp;preuve";
      echo " <em>(".link_to($preuves_str, "@preuve_presence_seance?seance=".$seance->id."&slug=".$parlementaire->slug).")</em>";
    }
    if ($seance->type == "hemicycle") {
      echo "<ul>";
      foreach ($seance->getTableMatiere() as $section) if ($section["section_id"] == $section["id"] && $section["id"] != 1 && !(preg_match("/(ordre\sdu\sjour|suspension\sde\séance)/i", $section["titre"])))
        echo "<li>".link_to($section["titre"], "@interventions_seance?seance=".$seance->id."#table_".$section["id"])."</li>";
      echo "</ul>";
    }
    echo "</li>";
  }
  echo '</ul>';
} ?>
