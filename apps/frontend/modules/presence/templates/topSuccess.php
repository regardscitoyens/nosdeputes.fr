<div class="temp">
<h1>Top des présences</h1>
<?php $cpt = 0 ; foreach ($top as $t) : $cpt++;?>
<p><?php echo $cpt; ?> - <?php echo link_to($t['nom'], '@parlementaire?slug='.$t['slug']); ?> (<?php echo link_to($t['nb']." presence(s)", '@parlementaire_presences?slug='.$t['slug']); ?>)</p>
<?php endforeach; ?>
</div>