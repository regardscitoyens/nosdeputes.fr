<p><?php $n_presences = count($presences); echo $n_presences." présences en commission depuis "; ?></p>
<ul>
<?php foreach($presences as $presence) : ?>
<?php $p = $presence->toArray(true); ?>
<?php $s = $p['Seance'];?>
<?php $o = $s['Organisme'];?>
<?php $pr = $p['Preuves'];?>
<?php $preuves = PreuvePresence::getPreuves($pr); ?>
<li><?php echo $s['type']; ?> : <?php echo $o['nom']; ?> (<a href="<?php echo url_for('@interventions_seance?seance='.$s['id']); ?>"><?php echo $s['date']; ?>, <?php echo $s['moment']; ?></a>) <em>(<?php echo $preuves; ?>)</em></li>
<?php endforeach; ?>
</ul>
