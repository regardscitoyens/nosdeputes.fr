<?php
$ct = 0;
$anciens = false;
if (isset($list)) {
  if (!isset($colonnes))
    $colonnes = 3;
  if (isset($imp)) {
    if (isset($deputes[0]->fonction)) {
      $fonction = $deputes[0]->fonction;
      foreach ($deputes as $depute) if ($depute->sexe === "H") {
        $fonction = $depute->fonction;
        break;
      }
      if (isset($deputes[0]->fin_fonction)) {
        $anciens = true;
        $fonction = "Ancien ".$fonction;
      }
    } else {
      $pluriel = (count($deputes) > 1 ? "s" : "");
      $fonction = "Ancien député";
      $anciens = true;
    }
    echo '<h3 class="aligncenter'.($anciens ? " anciens" : "").'">'.ucfirst(preg_replace('/d(u|e)s /', 'd\\1 ', (count($deputes) > 1 ? preg_replace('/(,)? /', 's\\1 ', (preg_match('/(spécial|général)/i', $fonction) ? preg_replace('/al$/', 'aux', $fonction) : $fonction)) : $fonction))).(count($deputes) > 1 && !preg_match('/(spécial|général|droit|bureau)$/i', $fonction) ? 's' : '').'</h3>';
  }
  echo '<table summary="Députés'.(isset($lettre) ? ' dont le nom commence par '.$lettre : '').'"><tr>';
  $totaldep = count($deputes);
  $div = floor($totaldep/$colonnes)+1;
  if ($div > 1 && $totaldep % $colonnes == 0)
    $div--;
  $td = 0;
  if ($totaldep == 1) {
    if ($colonnes == 2)
      echo '<td class="list_td_small"/>';
    else echo '<td/>';
    $td++;
  } else if ($colonnes != 2 && ($totaldep == 2 || $totaldep == 4))
    echo '<td class="list_td_small"/>';
  echo '<td>';
}
foreach($deputes as $depute) {
  $url_depute = url_for('@parlementaire?slug='.$depute->slug);
  $ct++;
  $id_circo = preg_replace('/^(\d[\dab])$/', '0\\1', strtolower(Parlementaire::getNumeroDepartement($depute->nom_circo))).'-'.sprintf('%02d', $depute->num_circo); ?>
  <a href="<?php echo $url_depute; ?>"><div class="list_dep jstitle phototitle block<?php if ($anciens || !$depute->isEnMandat()) echo ' anciens'; if (isset($circo) && ($depute->isEnMandat() || myTools::isFinLegislature())) echo ' dep_map dep'.$id_circo.'" id="'.sprintf('%03d', $depute->id).$id_circo; ?>" title="<?php echo $depute->nom.' -- '.$depute->getMoyenStatut(); ?>">
    <span class="urlphoto" title="<?php echo $url_depute; ?>"></span>
    <span class="list_nom">
      <?php echo $depute->getNomPrenom(); ?>
    </span>
    <span class="list_right"><?php
      if ($anciens) {
        echo '<span class="clearboth">';
        if (isset($depute->fin_fonction))
          echo 'du '.myTools::displayDate($depute->debut_fonction).' au '.myTools::displayDate($depute->fin_fonction);
        else echo $depute->old_fonction;
        echo '</span><br/>';
      }
      if (isset($circo)) {
        echo '<span class="list_num_circo">';
        $string = preg_replace('/(è[rm]e)/', '<sup>\1</sup>', $depute->getNumCircoString());
        if (isset($dept))
          $string = $depute->getNumDepartement().'&nbsp;&mdash;&nbsp;'.preg_replace("/nscription/", "", $string);
        echo $string.'</span>';
      } else echo $depute->nom_circo;
    ?></span><br/>
    <span class="list_left">
      <?php echo preg_replace('/\s([A-Z\-]+)$/', ' <span class="c_'.strtolower($depute->getGroupeAcronyme()).'">'."\\1</span>", $depute->getStatut()); ?>
    </span>
    <span class="list_right">
    <?php if ($depute->nb_commentaires) {
      echo '<span class="list_com">'.$depute->nb_commentaires.'&nbsp;commentaire';
      if ($depute->nb_commentaires > 1) echo 's';
      echo '</span>';
    } ?>
    </span><br/>
  </div></a>
  <?php if (isset($list) && $ct % $div == 0 && $ct != $totaldep && $totaldep != 1) {
    $td++;
    echo '</td><td class="list_borderleft">';
  }
}
if (isset($list)) {
  echo '</td>';
  if (($colonnes == 2 && $totaldep == 1) || ($colonnes != 2 && ($totaldep == 2 || $totaldep == 4)))
    echo '<td class="list_td_small"/>';
  else while ($td < $colonnes - 1) {
    $td++;
    echo '<td/>';
  }
  echo '</tr></table>';
}
?>
