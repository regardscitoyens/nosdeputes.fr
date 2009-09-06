<div class="temp">
<h1>Présence de <?php echo $parlementaire->nom; ?></h1>
<p><?php $n_presences = count($presences); echo $n_presences." présences en commission depuis "; ?></p>
<ul>
<?php foreach($presences as $presence) : ?>
<?php $s = $presence->getSeance(); ?>
<?php $o = $s->getOrganisme(); ?>
<?php $pr = $presence->getPreuves(); ?>
<?php $nbpreuves = $presence->getNbPreuves(); ?>
<li><?php echo $s['type']; ?> : <?php echo $o['nom']; ?> (<a href="<?php echo url_for('@interventions_seance?seance='.$s['id']); ?>"><?php echo myTools::displayDate($s['date']); ?>, <?php echo $s['moment']; ?></a>) <em><a href="<?php echo url_for('@preuve_presence_seance?seance='.$s['id'].'&slug='.$parlementaire->slug); ?>">(<?php echo ($nbpreuves>1) ? "$nbpreuves preuves" : "1 preuve"; ?>)</a></em></li>
<?php endforeach; ?>
</ul>
</div>
