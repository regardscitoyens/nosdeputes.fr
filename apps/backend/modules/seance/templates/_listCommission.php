<?php if (! isset($nofuse)) { ?> 
  <form action="<?php echo url_for('@fuse'); ?>" method="get">
  <input type="hidden" name="type" value="seance" />
  <input type="hidden" name="id" value="<?php echo $orga; ?>" />
  <div style="text-align:right;"><input type="submit" value="Fusionner les séances" /></div>
<?php } ?>
<table cellspacing="0">
<thead>
  <tr>
    <th class="sf_admin_text">Id</th>
    <th class="sf_admin_text">Date</th>
    <th class="sf_admin_text">Moment</th>
    <th class="sf_admin_text">Session</th>
    <th class="sf_admin_text">Tags</th>
    <th class="sf_admin_text">Comments</th>
    <th class="sf_admin_text">Interventions</th>
    <th class="sf_admin_text">Presents<br/>(sources)</th>
    <th id="sf_admin_list_th_actions">Actions</th>
    <?php if (! isset($nofuse)) echo '<th id="sf_admin_list_th_actions">Fusion<br/>bad -> good</th>'; ?>
  </tr>
</thead>
<tbody>
  <?php $row = 0; $date0 = ""; foreach ($seances as $seance) { ?>
    <tr class="sf_admin_row <?php if ($row == 0) { $row++; echo 'odd'; } else { $row--; echo 'even'; } ?>">
      <td class="sf_admin_text"><a href="<?php echo link_tof('interventions_seance', array('seance' => $seance['id'])); ?>"><?php echo $seance['id']; ?></a></td>
      <td class="sf_admin_date">
        <?php if ($date0 != $seance['date']) {
          $date0 = $seance['date'];
          echo '<a href="'.link_tof('interventions_seance', array('seance' => $seance['id'])).'">'.myTools::displayDateSemaine($seance['date']).'</a>';
        } else echo '<span style="padding-left:20px;">"</span></td>'; ?>
      <td class="sf_admin_text"><a href="<?php echo link_tof('interventions_seance', array('seance' => $seance['id'])); ?>"><?php echo $seance['moment']; ?></a></td>
      <td class="sf_admin_text"><a href="<?php echo link_tof('interventions_seance', array('seance' => $seance['id'])); ?>"><?php echo $seance['session']; ?></a></td>
      <td class="sf_admin_boolean"> <?php if ($seance['tagged']) echo '<img src="/sfDoctrinePlugin/images/tick.png" title="Checked" alt="Checked"/>'; ?> </td>
      <td class="sf_admin_text"><?php echo $seance['nb_commentaires']; ?></td>
      <td class="sf_admin_text"><?php echo $seance['n_interventions']; ?></td>
      <td class="sf_admin_text"><a href="<?php echo link_tof('presents_seance', array('seance' => $seance['id'])); ?>"><?php echo $seance['presents']; ?> (<?php echo $seance['sources']; ?>)</a></td>
      <td><ul class="sf_admin_td_actions">
        <li class="sf_admin_action_edit"><a href="/backend_dev.php/seance/<?php echo $seance['id']; ?>/edit">Edit</a></li>
        <?php if ($seance['n_interventions'] == 0) echo '<li class="sf_admin_action_delete">'.link_to('Supprimer', '@seance_suppr?id='.$seance['id']).'</li>'; ?>
      </ul></td>
      <?php if (! isset($nofuse)) echo '<td><input type="radio" name="bad" value="'.$seance['id'].'" /> -><input type="radio" name="good" value="'.$seance['id'].'" /></td>'; ?>
    </tr>
  <?php } ?>
</tbody>
</table>
<?php if (! isset($nofuse)) { ?>
  <div style="text-align:right;"><input type="submit" value="Fusionner les séances" /></div>
  </form>
<?php } ?>
