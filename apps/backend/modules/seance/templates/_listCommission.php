<?php if (! isset($nofuse)) { ?> 
  <form action="<?php echo url_for('@fuse_nseances'); ?>" method="get">
  <input type="hidden" name="id" value="<?php echo $orga; ?>" />
  <div style="text-align:right;"><input type="submit" name="formaction" value="Supprimer les seances" /><input type="submit" name="formaction" value="Fusionner les seances" /></div>
<?php } ?>
<table cellspacing="0">
<thead>
  <tr>
    <th class="sf_admin_text">Id</th>
    <th class="sf_admin_text">Date</th>
    <th class="sf_admin_text">Moment</th>
    <th class="sf_admin_text">Session</th>
    <th class="sf_admin_text">Org_ID</th>
    <th class="sf_admin_text">Tags</th>
    <th class="sf_admin_text">Comments</th>
    <th class="sf_admin_text">Interventions</th>
    <th class="sf_admin_text">Presents<br/>(sources)</th>
    <th id="sf_admin_list_th_actions">Actions</th>
    <?php if (! isset($nofuse)) echo '<th id="sf_admin_list_th_actions">Fusion<br/>bad -> good</th>'; ?>
  </tr>
</thead>
<tbody>
  <?php $n_date = 0; $date0 = ""; $row = 0; $n_seance = 0; foreach ($seances as $seance) {
    $n_seance++;
    if ($date0 != $seance['date']) {
      $n_date++;
      if ($row == 0) $row++;
      else $row--;
    } ?>
    <tr class="sf_admin_row <?php if ($row == 0) echo 'odd'; else echo 'even'; ?>">
      <td class="sf_admin_text"><a href="<?php echo link_tof('interventions_seance', array('seance' => $seance['id'])); ?>"><?php echo $seance['id']; ?></a></td>
      <td class="sf_admin_date">
        <?php if ($date0 != $seance['date']) {
          $date0 = $seance['date'];
          echo '<a href="'.link_tof('interventions_seance', array('seance' => $seance['id'])).'">'.myTools::displayDateSemaine($seance['date']).'</a>';
        } else echo '<span style="padding-left:20px;">"</span></td>'; ?>
      <td class="sf_admin_text"><a href="<?php echo link_tof('interventions_seance', array('seance' => $seance['id'])); ?>"><?php echo $seance['moment']; ?></a></td>
      <td class="sf_admin_text"><a href="<?php echo link_tof('interventions_seance', array('seance' => $seance['id'])); ?>"><?php echo $seance['session']; ?></a></td>
      <td class="sf_admin_text"><?php if ($seance['organisme_id']) echo link_to($seance['organisme_id'], '@commission?id='.$seance['organisme_id']); ?></td>
      <td class="sf_admin_boolean"> <?php if ($seance['tagged']) echo '<img src="/sfDoctrinePlugin/images/tick.png" title="Checked" alt="Checked"/>'; ?> </td>
      <td class="sf_admin_text"><?php echo $seance['nb_commentaires']; ?></td>
      <td class="sf_admin_text"><?php echo $seance['n_interventions']; ?></td>
      <td class="sf_admin_text"><a href="<?php echo link_tof('presents_seance', array('seance' => $seance['id'])); ?>"><?php echo $seance['presents']; ?> (<?php if ($seance['n_interventions'] <= $seance['presents'] && $seance['n_interventions'] > 0) echo '2'; else echo $seance['sources']; ?>)</a></td>
      <td><ul class="sf_admin_td_actions">
        <li class="sf_admin_action_edit"><a href="/backend_dev.php/seance/<?php echo $seance['id']; ?>/edit">Edit</a></li>
        <?php if ($seance['n_interventions'] == 0 && !isset($nofuse)) echo '<li class="sf_admin_action_delete">'.link_to('Supprimer', '@seance_suppr?id='.$seance['id']).'&nbsp;<input type="checkbox" name="suppr'.$n_seance.'" value="'.$seance['id'].'" /></li>'; ?>
      </ul></td>
      <td><?php if (! isset($nofuse)) {
        if ($seance['n_interventions'] == 0) echo '<input type="radio" name="bad'.$n_date.'" value="'.$seance['id'].'" /> -> ';
        else echo '&nbsp;&nbsp;-> <input type="radio" name="good'.$n_date.'" value="'.$seance['id'].'" />';
      } ?></td>
    </tr>
  <?php } ?>
<input type="hidden" name="dates" value="<?php echo $n_date; ?>" />
<input type="hidden" name="seances" value="<?php echo $n_seance; ?>" />
</tbody>
</table>
<?php if (! isset($nofuse)) { ?>
  <div style="text-align:right;"><input type="submit" name="formaction" value="Supprimer les seances" /><input type="submit" name="formaction" value="Fusionner les seances" /></div>
  </form>
<?php } ?>
