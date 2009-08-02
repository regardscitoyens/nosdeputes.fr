<h1>Top des interventions</h1>
<?php $cpt = 0 ; foreach ($top as $t) : $cpt++;?>
<p><?php echo $cpt; ?> - <?php echo link_to($t['nom'], '@parlementaire?slug='.$t['slug']); ?> (<?php echo link_to($t['nb'].' intervention(s)', '@parlementaire_interventions?slug='.$t['slug'])?>)</p>
<?php endforeach; ?>