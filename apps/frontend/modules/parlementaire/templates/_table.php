<?php
$ct = 0;
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
    } else { 
      $pluriel = (count($deputes) > 1 ? "s" : ""); 
      $fonction = "Ancien député"; 
    }
    echo '<h3 class="aligncenter">'.ucfirst(preg_replace('/d(u|e)s /', 'd\\1 ', (count($deputes) > 1 ? preg_replace('/(,)? /', 's\\1 ', (preg_match('/(spécial|général)/i', $fonction) ? preg_replace('/al$/', 'aux', $fonction) : $fonction)) : $fonction))).(count($deputes) > 1 && !preg_match('/(spécial|général|droit|bureau)$/i', $fonction) ? 's' : '').'</h3>';
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
  $ct++; ?>
  <div class="list_dep<?php if (isset($circo) && $depute->fin_mandat == null) echo ' dep_map" id="dep'.preg_replace('/^(\d[\dab])$/', '0\\1', strtolower(Parlementaire::getNumeroDepartement($depute->nom_circo))).'-'.sprintf('%02d', $depute->num_circo); ?>" onclick="document.location='<?php echo $url_depute; ?>'"><span title="<?php echo $depute->nom.' -- '.$depute->getMoyenStatut(); ?>" class="jstitle phototitle block"><a class="urlphoto" href="<?php echo $url_depute; ?>"></a>
    <span class="list_nom">
      <a href="<?php echo $url_depute; ?>"><?php echo $depute->getNomPrenom(); ?></a>
    </span>
    <span class="list_right"><a href="<?php echo $url_depute; //if (!isset($circo)) echo url_for('@list_parlementaires_departement?departement='.$depute->nom_circo); else echo url_for('@parlementaire?slug='.$depute->slug); ?>"><?php
      if (isset($circo)) {
        echo '<span class="list_num_circo">';
        $string = preg_replace('/(è[rm]e)/', '<sup>\1</sup>', $depute->getNumCircoString());
        if (isset($dept))
          $string = $depute->getNumDepartement().'&nbsp;&mdash;&nbsp;'.preg_replace("/nscription/", "", $string);
        echo $string.'</span></a>';
      } else echo $depute->nom_circo;
    ?></a></span><br/>
    <span class="list_left">
      <?php echo preg_replace('/\s([A-Z]+)$/', ' <a href="'.$url_depute.'"><span class="c_'.strtolower($depute->getGroupeAcronyme()).'">'."\\1</span></a>", $depute->getStatut()); ?>
    </span>
    <span class="list_right"><?php
      if ($depute->nb_commentaires) {
        echo '<a href="'.$url_depute.'"><span class="list_com">'.$depute->nb_commentaires.'&nbsp;commentaire';
        if ($depute->nb_commentaires > 1) echo 's';
        echo '</span></a>';
      }
    ?>
    </span><br/>
  </span></div>
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
