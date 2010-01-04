<?php function link_tof($name, $parameters) { return sfProjectConfiguration::getActive()->generateFrontendUrl($name, $parameters); } ?>
<div id="sf_admin_container">
  <h1>Fusionner des <?php echo $type; ?>s</h1>
  <div id="sf_admin_header"> </div>
  <div id="sf_admin_content">
    <h2 style="text-align:center;">ATTENTION !!! Voulez-vous vraiment faire fusionner ces deux <?php echo $type; ?>s<?php if ($type == "commission") echo ' et obtenir le résultat ci-dessous ?</h2><p style="text-align:center;">Veuillez confirmer avec le <a href="#fuse">lien en bas de page</a></p>'; else echo ' ?</h2>'; ?>
    <div class="sf_admin_list">
      <?php if ($type == "commission")
        include_partial('organisme/commission', array('article' => $article, 'seances' => $seances, 'deputes' => $deputes, 'nofuse' => 1));
      else include_partial('seance/listCommission', array('seances' => $seances, 'nofuse' => 1)); ?>
    </div>
    <p id="fuse" style="text-align:center;"><a href="<?php if ($type == "commission") echo url_for('@fuse_commissions?bad='.$bad.'&good='.$good); else echo url_for('@fuse_seances?id='.$orga.'&bad='.$bad.'&good='.$good); ?>">FUSIONNER</a></p>
  </div>
  <div id="sf_admin_footer"><br/></div>
</div>

