<?php
  if (isset($lettre)) {
    echo '<div class="list_choix" id="'.$lettre.'">';
    if (isset($listlettres)) {
      $link = '@list_parlementaires#';
    } else {
      $link = '@list_parlementaires_alpha?search=';
      $listlettres = range('A','Z');
    }
    foreach($listlettres as $i) {
      if ($i != $lettre) echo link_to($i , $link.$i);
      else echo '<big><strong>'.$i.'</strong></big>';
      echo '&nbsp;&nbsp;';
    }
    echo '</div>';
  }
  echo '<div class="list_table"><table><tr><td>';
  $totaldep = count($deputes);
  $div = floor($totaldep/3)+1;
  if ($div > 1 && $totaldep % 3 == 0)
    $div--;
  $ct = 0;
  $td = 0;
  foreach($deputes as $depute) {
    $ct++; ?>
    <a href="<?php echo url_for('@parlementaire?slug='.$depute->slug); ?>"><div class="list_dep">
      <div class="list_left">
      <span class="list_nom"><?php echo $depute->getNomPrenom(); ?></span><br/>&nbsp;
        <?php echo preg_replace('/\s([A-Z]+)$/', ' <span class="couleur_'.strtolower($depute->getGroupeAcronyme()).'">'."\\1</span>", $depute->getStatut()); ?>
      </div>
      <div class="list_details">
        <?php echo $depute->nom_circo; ?>
        <?php if ($depute->nb_commentaires > 0) {
          echo '<br/><span class="list_com">'.$depute->nb_commentaires.' commentaire';
          if ($depute->nb_commentaires > 1) echo 's';
          echo '</span>';
        } ?>
      </div>
    </div></a>
    <?php if ($ct % $div == 0 && $ct != $totaldep) {
      $td++;
      echo '</td><td class="list_borderleft">';
    }
  } ?>
  </td><?php while ($td < 2) { $td++; echo '<td/>'; } ?></tr></table>
<div class="suivant"><a href="#">Haut de page</a></div>
</div>
