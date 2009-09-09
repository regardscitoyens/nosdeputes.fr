<div class="temp">
<?php
$titre = 'Présence en hémicycle et commissions';
echo include_component('parlementaire', 'header', array('parlementaire' => $parlementaire, 'titre' => $titre));
?>
<p><?php $n_presences = count($presences); echo $n_presences." présences en commission depuis ".myTools::displayDate($parlementaire->debut_mandat); ?></p>
<ul>
<?php foreach($presences as $presence) : ?>
<?php $s = $presence->getSeance(); ?>
<?php $o = $s->getOrganisme(); ?>
<?php $nbpreuves = $presence->getNbPreuves(); ?>
<li><?php echo $s['type']; ?> : <?php echo $o['nom']; ?> <?php echo link_to($s->getTitre(), '@interventions_seance?seance='.$s['id']); ?> <em>(<?php echo link_to(($nbpreuves>1) ? "$nbpreuves preuves" : "1 preuve", '@preuve_presence_seance?seance='.$s['id'].'&slug='.$parlementaire->slug); ?>)</em></li>
<?php endforeach; ?>
</ul>
</div>
