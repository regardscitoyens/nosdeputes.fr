<?php function link_tof($name, $parameters) { return sfProjectConfiguration::getActive()->generateFrontendUrl($name, $parameters); } ?>
<div id="sf_admin_container">
  <h1>Fusionner des <?php echo $type; ?>s&nbsp;&mdash <?php if ($type == "commission") echo link_to("Retour aux commissions", '@list_commissions'); else if (preg_match('/^(\d+),(\d+)$/', $orga, $match)) echo link_to("Retour à la fusion", '@fuse?type=commission&bad='.$match[1].'&good='.$match[2]); else echo link_to("Retour à la commission", '@commission?id='.$orga); ?></h1>
  <div id="sf_admin_header"> </div>
  <div id="sf_admin_content">
    <?php if (isset($result)) {
      echo '<p style="text-align:center;">';
      if ($result == "wrong") echo "ATTENTION : Vous ne pouvez pas faire fusionner une séance comportant des interventions";
      else if ($result == "wrongdate") echo "ATTENTION : Fusion impossible, les deux séances n'ont pas la même date";
      else if ($result == "fail") echo "ATTENTION : Echec de la fusion des deux séances";
      else echo "Fusion des séances réussie";
      echo '</p>';
    } ?>
    <?php if (isset($doublons)) echo '<p style="text-align:center;">ATTENTION : les séances ci-dessous vont faire doublon, veuillez les fusionner avant de fusionner les commissions</p>'; ?>
     <h2 style="text-align:center;">ATTENTION !!! Voulez-vous vraiment faire fusionner ces <?php echo $type; ?>s<?php if ($type == "commission") echo ' et obtenir le résultat ci-dessous ?</h2><p style="text-align:center;">Veuillez confirmer avec le <a href="#fuse">lien en bas de page</a></p>'; else echo ' ?</h2>'; ?>
    <div class="sf_admin_list">
      <?php if ($type == "commission")
        include_partial('organisme/commission', array('article' => $article, 'seances' => $seances, 'deputes' => $deputes, 'orga' => $bad.','.$good));
      else include_partial('seance/listCommission', array('seances' => $seances, 'nofuse' => 1)); ?>
    </div>
    <?php if (!(isset($doublons) && $doublons > 1)) {
      echo '<form id="fuse" style="text-align:center;" action="';
      if ($type == "commission") {
        echo url_for('@fuse_commissions').'" method="post">';
        echo '<input type="hidden" name="bad" value="'.$bad.'" />';
        echo '<input type="hidden" name="good" value="'.$good.'" />';
      } else {
        echo url_for('@fuse_seances?id='.$orga).'" method="post">';
        echo '<input type="hidden" name="bad" value="'.$bads.'" />';
        echo '<input type="hidden" name="good" value="'.$goods.'" />';
      }
      echo '<input type="submit" value="Fusionner" /></div>';
    } ?>
  </div>
  <div id="sf_admin_footer"><br/></div>
</div>

