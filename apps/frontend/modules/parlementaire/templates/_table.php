<?php
$ct = 0;
if (isset($list)) {
  if (!isset($colonnes))
    $colonnes = 3;
  if (isset($imp)) {
    $fonction = $senateurs[0]->fonction;
    foreach ($senateurs as $senateur) if ($senateur->sexe === "H") {
      $fonction = $senateur->fonction;
       break;
    }
    echo '<h3 class="aligncenter">'.ucfirst(preg_replace('/d(u|e)s /', 'd\\1 ', (count($senateurs) > 1 ? preg_replace('/(,)? /', 's\\1 ', (preg_match('/(spécial|général)/i', $fonction) ? preg_replace('/al$/', 'aux', $fonction) : $fonction)) : $fonction))).(count($senateurs) > 1 && !preg_match('/(spécial|général|droit|bureau)$/i', $fonction) ? 's' : '').'</h3>';
  }
  echo '<table summary="Sénateurs'.(isset($lettre) ? ' dont le nom commence par '.$lettre : '').'"><tr>';
  $totaldep = count($senateurs);
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
foreach($senateurs as $senateur) {
  $ct++; ?>
  <div class="list_dep<?php if (isset($circo) && $senateur->fin_mandat == null) echo ' dep_map" id="dep'.preg_replace('/^(\d[\dab])$/', '0\\1', strtolower(Parlementaire::getNumeroDepartement($senateur->nom_circo))).'-'.sprintf('%02d', $senateur->num_circo); ?>" onclick="document.location='<?php echo url_for('@parlementaire?slug='.$senateur->slug); ?>'"><span title="<?php echo $senateur->nom.' -- '.$senateur->getMoyenStatut(); ?>" class="jstitle phototitle block"><a class="urlphoto" href="<?php echo url_for('@parlementaire?slug='.$senateur->slug); ?>"></a>
    <span class="list_nom">
      <a href="<?php echo url_for('@parlementaire?slug='.$senateur->slug); ?>"><?php echo $senateur->getNomPrenom(); ?></a>
    </span>
    <span class="list_right"><a href="<?php if (!isset($circo)) echo url_for('@list_parlementaires_departement?departement='.$senateur->nom_circo); else echo url_for('@parlementaire?slug='.$senateur->slug); ?>"><?php
      if (isset($circo)) {
        echo '<span class="list_num_circo">';
        if (isset($dept))
          $string = $senateur->getNumDepartement();
        echo $string.'</span></a>';
      } else echo $senateur->nom_circo; 
    ?></a></span><br/>
    <span class="list_left">
      <?php echo preg_replace('/\s([A-Z\-]+)$/', ' <a href="'.url_for('@list_parlementaires_groupe?acro='.$senateur->groupe_acronyme).'"><span class="couleur_'.strtolower($senateur->getGroupeAcronyme()).'">'."\\1</span></a>", $senateur->getStatut()); ?>
    </span>
    <span class="list_right"><?php
      if (!$senateur->nb_commentaires)
        echo "0&nbsp;commentaire";
      else {
        echo '<a href="'.url_for('@parlementaire_commentaires?slug='.$senateur->slug).'"><span class="list_com">'.$senateur->nb_commentaires.'&nbsp;commentaire';
        if ($senateur->nb_commentaires > 1) echo 's';
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
