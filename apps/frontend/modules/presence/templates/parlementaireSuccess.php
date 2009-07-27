<h1>Présence de <? echo $parlementaire->nom; ?></h1>
<p><?php $n_presences = count($presences); echo $n_presences." présences en commission depuis "; ?></p>
<ul>
<?php foreach($presences as $presence) : ?>
<?php $p = $presence->toArray(true); ?>
<?php $s = $p['Seance'];?>
<?php $o = $s['Organisme'];?>
<?php $pr = $p['Preuves'];?>
<?php $nbpreuves = $presence->getNbPreuves(); ?>
<li><?php echo $s['type']; ?> : <?php echo $o['nom']; ?> (<a href="<?php echo url_for('@interventions_seance?seance='.$s['id']); ?>"><?php echo $s['date']; ?>, <?php echo $s['moment']; ?></a>) <em><a href="<?php echo url_for('@preuve_presence_seance?seance='.$s['id'].'&slug='.$parlementaire->slug); ?>">(<?php echo ($nbpreuves>1) ? "$nbpreuves preuves" : "1 preuve"; ?>)</a></em></li>
<?php endforeach; ?>
</ul>
