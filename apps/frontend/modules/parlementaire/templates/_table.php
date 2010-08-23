<?php
$ct = 0;
if (isset($list)) {
  echo '<table><tr>';
  $totaldep = count($deputes);
  $div = floor($totaldep/3)+1;
  if ($div > 1 && $totaldep % 3 == 0)
    $div--;
  $td = 0;
  if ($totaldep == 1) {
    echo '<td/><td>';
    $td++;
  } else if ($totaldep == 2 || $totaldep == 4)
    echo '<td class="list_td_small"/><td>';
  else echo '<td>';
}
foreach($deputes as $depute) {
  $ct++; ?>
  <a href="<?php echo url_for('@parlementaire?slug='.$depute->slug); ?>"><div class="list_dep">
    <div class="list_nom">
      <?php echo $depute->getNomPrenom(); ?>
    </div>
    <div class="list_right"><?php
      if (isset($circo)) {
        echo '<span class="list_num_circo">';
        $string = preg_replace('/(è[rm]e)/', '<sup>\1</sup>', $depute->getNumCircoString());
        if (isset($dept))
          $string = $depute->getNumDepartement().'&nbsp;&mdash;&nbsp;'.preg_replace("/nscription/", "", $string);
        echo $string.'</span>';
      } else echo $depute->nom_circo; 
    ?></div><div class="clear"/>
    <div class="list_left">
      <?php if (isset($imp)) {
          echo ' '.$depute->fonction;
          if (!isset($nogroupe))
            echo '&nbsp;&mdash;&nbsp;<span class="couleur_'.strtolower($depute->getGroupeAcronyme()).'">'.$depute->getGroupeAcronyme().'</span>';
        } else
          echo preg_replace('/\s([A-Z]+)$/', ' <span class="couleur_'.strtolower($depute->getGroupeAcronyme()).'">'."\\1</span>", $depute->getStatut()); ?>
    </div>
    <div class="list_right"><?php
      if (!$depute->nb_commentaires)
        echo "0&nbsp;commentaire";
      else {
        echo '<span class="list_com">'.$depute->nb_commentaires.'&nbsp;commentaire';
        if ($depute->nb_commentaires > 1) echo 's'; echo '</span>';
      }
    ?></div>
    </div>
  </div></a>
  <?php if (isset($list) && $ct % $div == 0 && $ct != $totaldep && $totaldep != 1) {
    $td++;
    echo '</td><td class="list_borderleft">';
  }
}
if (isset($list)) {
  echo '</td>';
  if ($totaldep == 2 || $totaldep == 4)
    echo '<td class="list_td_small"/>';
  else while ($td < 2) {
    $td++;
    echo '<td/>';
  }
  echo '</tr></table>';
}
?>
