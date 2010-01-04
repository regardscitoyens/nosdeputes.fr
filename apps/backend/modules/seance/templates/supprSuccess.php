<?php function link_tof($name, $parameters) { return sfProjectConfiguration::getActive()->generateFrontendUrl($name, $parameters); } ?>
<div id="sf_admin_container">
  <h1>Suppression de séances&nbsp;: <?php echo $ids ?></h1>
  <p style="text-align:center;"><a href="<?php if ($orga) echo url_for('@commission?id='.$orga->id).'">Retour à la commission</a>'; else echo url_for('@list_commissions').'#seances">Retour à la liste des séances orphelines</a>'; ?></p>
  <div id="sf_admin_header"> </div>
  <div id="sf_admin_content">
    <h2 style="text-align:center;">ATTENTION !!! La suppression entraînera la suppression des présences et preuves de présence listées ci-dessous !</h2>
    <p style="text-align:center;">Veuillez confirmer avec le <a href="#suppr">lien en bas de page</a></p>
    <div class="sf_admin_list">
      <?php include_partial('seance/listCommission', array('seances' => $seances, 'nofuse' => 1)); ?>
    </div>
    <h2>Preuves de présences associées&nbsp;:</h2><p style="text-align:center;">
    <?php $ct = count($presences);
    for ($i=0; $i<$ct; $i++) {
     echo $presences[$i];
     if ($i != $ct-1) echo ' &nbsp;&mdash ';
    } ?></p>
    <p id="suppr" style="text-align:center;">Attention !!! Si vous voulez vraiment supprimer cette séance et les références associées, veuillez cliquer sur le lien ci-dessous&nbsp;:</p><p style="text-align:center;"><b><?php echo link_to('SUPPRIMER', '@seance_suppr_ok?id='.$ids); ?></b></p>
  </div>
  <div id="sf_admin_footer"><br/></div>
</div>

