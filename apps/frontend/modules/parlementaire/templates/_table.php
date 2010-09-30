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
  <div class="list_dep<?php if (isset($circo) && $depute->fin_mandat == null) echo ' dep_map" id="dep'.preg_replace('/^(\d[\dab])$/', '0\\1', strtolower(Parlementaire::getNumeroDepartement($depute->nom_circo))).'-'.sprintf('%02d', $depute->num_circo); ?>" onclick="document.location='<?php echo url_for('@parlementaire?slug='.$depute->slug); ?>'">
    <span class="list_nom">
      <a href="<?php echo url_for('@parlementaire?slug='.$depute->slug); ?>"><?php echo $depute->getNomPrenom(); ?></a>
    </span>
    <span class="list_right"><a href="<?php if (!isset($circo)) echo url_for('@list_parlementaires_departement?departement='.$depute->nom_circo); else echo url_for('@parlementaire?slug='.$depute->slug); ?>"><?php
      if (isset($circo)) {
        echo '<span class="list_num_circo">';
        $string = preg_replace('/(è[rm]e)/', '<sup>\1</sup>', $depute->getNumCircoString());
        if (isset($dept))
          $string = $depute->getNumDepartement().'&nbsp;&mdash;&nbsp;'.preg_replace("/nscription/", "", $string);
        echo $string.'</span></a>';
      } else echo $depute->nom_circo; 
    ?></a></span><br/>
    <span class="list_left">
      <?php if (isset($imp)) {
          echo ' '.$depute->fonction;
          if (!isset($nogroupe))
            echo '&nbsp;&mdash;&nbsp;<a href="'.url_for('@list_parlementaires_groupe?acro='.$depute->groupe_acronyme).'"><span class="couleur_'.strtolower($depute->getGroupeAcronyme()).'">'.$depute->getGroupeAcronyme().'</span></a>';
        } else
          echo preg_replace('/\s([A-Z]+)$/', ' <a href="'.url_for('@list_parlementaires_groupe?acro='.$depute->groupe_acronyme).'"><span class="couleur_'.strtolower($depute->getGroupeAcronyme()).'">'."\\1</span></a>", $depute->getStatut()); ?>
    </span>
    <span class="list_right"><?php
      if (!$depute->nb_commentaires)
        echo "0&nbsp;commentaire";
      else {
        echo '<a href="'.url_for('@parlementaire_commentaires?slug='.$depute->slug).'"><span class="list_com">'.$depute->nb_commentaires.'&nbsp;commentaire';
        if ($depute->nb_commentaires > 1) echo 's';
        echo '</span></a>';
      }
    ?>
    </span><br/>
  </div>
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
