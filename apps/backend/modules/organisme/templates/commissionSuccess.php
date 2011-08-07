<?php $sf_response->setTitle($orga->nom);
function link_tof($name, $parameters) { return sfProjectConfiguration::getActive()->generateFrontendUrl($name, $parameters); } ?>
<div id="sf_admin_container">
  <h1><?php echo link_to($orga->nom, '@commission?id='.$orga->id); ?> (<a href="<?php echo link_tof('list_parlementaires_organisme', array('slug' => $orga['slug'])); ?>">lien frontend</a>)</h1>
  <p style="text-align:center;"><a href="<?php echo url_for('@list_commissions'); ?>">Retour à la liste des commissions</a></p>
  <div id="sf_admin_header"> </div>
  <div id="sf_admin_content">
    <?php if ($suppr == 2) {
      echo '<p style="text-align:center;">';
      if ($delsea['id'] < 0)
        echo 'Attention : Veuillez supprimer les interventions de cette séance avant tout si vous souhaitez vraiment la supprimer';
      else {
        if ($delsea['id'] > 0)
          echo 'Séance '.$delsea['id'].' supprimée avec succès';
        else echo 'Attention, echec de la suppression de la séance';
        echo ' avec '.$delsea['pre'].' présents et '.$delsea['prp'].' preuves de présence';
      }
      echo '</p>';
    } else if ($suppr == 1) echo '<h2 style="text-align:center;">ATTENTION !!! La suppression entraînera la suppression des références listées ci-dessous !</h2><p style="text-align:center;">Les séances associées seront alors ophelines.<br/>Veuillez confirmer avec le <a href="#suppr">lien en bas de page</a></p>';
    else if (isset($result)) {
      echo '<p style="text-align:center;">';
      if ($result == "wrong") echo "ATTENTION : Vous ne pouvez pas faire fusionner une séance comportant des interventions";
      else if ($result == "wrongdate") echo "ATTENTION : Fusion impossible, les deux séances n'ont pas la même date";
      else if ($result == "fail") echo "ATTENTION : Echec de la fusion des deux séances";
      else echo "Fusion des séances réussie";
      echo '</p>';
    }
    if (($suppr == 2 || $suppr == 0) && count($deputes) == 0) echo '<p style="text-align:right;">'.link_to('Supprimer cette commission', '@commission_suppr?id='.$orga['id']).'</p>';
    include_partial('organisme/commission', array('article' => $article, 'seances' => $seances, 'deputes' => $deputes, 'orga' => $orga['id'])); ?>
    </div>
    <?php if ($suppr == 1) echo '<p id="suppr" style="text-align:center;">Attention !!! Si vous voulez vraiment supprimer cette commission et les références associées, veuillez cliquer sur le lien ci-dessous&nbsp;:</p><p style="text-align:center;"><b>'.link_to('SUPPRIMER CETTE COMMISSION', '@commission_suppr_ok?id='.$orga['id']).'</b></p>'; ?>
  </div>
  <div id="sf_admin_footer"><br/></div>
</div>

