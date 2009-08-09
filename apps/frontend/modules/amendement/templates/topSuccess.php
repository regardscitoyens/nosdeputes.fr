<div class="temp">
<h1>Top des amendements</h1>
<?php $cpt = 0 ; foreach ($top as $t) : $cpt++;?>
<p><?php echo $cpt; ?> - <?php echo link_to($t['nom'], '@parlementaire?slug='.$t['slug']); ?> (<?php echo link_to($t['nb'].' amendement(s)', '@parlementaire_amendements?slug='.$t['slug'])?>)</p>
<?php endforeach; ?>
</div>