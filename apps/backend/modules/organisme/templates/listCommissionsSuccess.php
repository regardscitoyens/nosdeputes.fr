<?php $sf_response->setTitle('Commissions');
function link_tof($name, $parameters) { return sfProjectConfiguration::getActive()->generateFrontendUrl($name, $parameters); } ?>
<div id="sf_admin_container">
  <h1>Commissions (Organismes de type "parlementaire")</h1>
  <div id="sf_admin_header"> </div>
  <div id="sf_admin_content">
    <p style="text-align:center;"><?php if (isset($delcom)) {
      if ($delcom['id'] != 0)
        echo 'Commission '.$delcom['id'].' supprimée avec succès';
      else echo 'Attention, echec de la suppression de la commission';
      echo ' avec '.$delcom['art'].' article, '.$delcom['dep'].' références à des députés et '.$delcom['sea'].' séances rendues orphelines';
    } else if (isset($delsea)) {
      if ($delsea['id'] < 0)
        echo 'Attention : Veuillez supprimer les interventions de cette séance avant tout si vous souhaitez vraiment la supprimer';
      else {
        if ($delsea['id'] > 0)
          echo 'Séance '.$delsea['id'].' supprimée avec succès';
        else echo 'Attention, echec de la suppression de la séance';
        echo ' avec '.$delsea['pre'].' présents et '.$delsea['prp'].' preuves de présence';
      }
    } else if (isset($result)) {
      if ($result == "wrong") echo "ATTENTION : Vous ne pouvez pas faire fusionner une commission ayant des inscrits";
      else if ($result == "wrongart") echo "ATTENTION : Fusion impossible, les deux commissions ont un article associé";
      else if ($result == "fail") echo "ATTENTION : Echec de la fusion des deux commissions";
      else if ($result == "wrongform") echo "ATTENTION : Vous devez avoir sélectionné des séances avant de les supprimer ou fusionner";
      else echo "Fusion des commissions réussie";
    } ?></p>
    <div class="sf_admin_list">
      <form action="<?php echo url_for('@fuse'); ?>" method="get">
      <input type="hidden" name="type" value="commission" />
      <div style="text-align:right;"><input type="submit" value="Fusionner les commissions" /></div>
      <table cellspacing="0">
      <thead>
        <tr>
          <th class="sf_admin_text">Id</th>
          <th class="sf_admin_text">Nom</th>
          <th class="sf_admin_text">Slug</th>
          <th class="sf_admin_text">Députés</th>
          <th class="sf_admin_text">Séances<br/>(tagged)</th>
          <th id="sf_admin_list_th_actions">Actions</th>
          <th id="sf_admin_list_th_actions">Fusion<br/>bad -> good</th>
        </tr>
      </thead>
      <tbody>
        <?php $row = 0; foreach ($orgas as $orga) { ?>
          <tr class="sf_admin_row <?php if ($row == 0) { $row++; echo 'odd'; } else { $row--; echo 'even'; } ?>">
            <td class="sf_admin_text"><b><a href="<?php echo link_tof('list_parlementaires_organisme', array('slug' => $orga['slug'])); ?>"><?php echo $orga['id']; ?></a></b></td>
            <td class="sf_admin_text"><?php echo link_to($orga['nom'], '@commission?id='.$orga['id']); ?></td>
            <td class="sf_admin_text"><?php echo link_to(preg_replace('/-/', '- ', $orga['slug']), '@commission?id='.$orga['id']); ?></td>
            <td class="sf_admin_text"><?php echo $orga['deputes']; ?></td>
            <td class="sf_admin_text"><?php echo $orga['seances']; if ($orga['tags']) { echo ' ('; if ($orga['deputes'] > 1) echo $orga['tags']/$orga['deputes']; else echo $orga['tags']; echo ')'; } ?></td>
            <td><ul class="sf_admin_td_actions">
              <li class="sf_admin_action_edit"><a href="/backend_dev.php/organisme/<?php echo $orga['id']; ?>/edit">Editer</a></li>
              <?php if ($orga['deputes'] == 0) echo '<li class="sf_admin_action_delete">'.link_to('Supprimer', '@commission_suppr?id='.$orga['id']).'</li>'; ?>
            </ul></td>
            <td>
              <?php if ($orga['deputes'] == 0) echo '<input type="radio" name="bad" value="'.$orga['id'].'" />';
              echo '&nbsp;&nbsp;-> <input type="radio" name="good" value="'.$orga['id'].'" />'; ?>
            </td>
          </tr>
        <?php } ?>
      </tbody>
      </table>
      <div style="text-align:right;"><input type="submit" value="Fusionner les commissions" /></div>
      </form>
      <?php if (count($seances)) {
        echo '<h2 id="seances">'.count($seances).' séances orphelines</h2>';
        include_partial('seance/listCommission', array('seances' => $seances, 'nofuse' => 1));
      } ?>
    </div>
  </div>
  <div id="sf_admin_footer"><br/></div>
</div>

