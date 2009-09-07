<div class="temp">
<h1><?php if ($orga = $seance->getOrganisme()) echo link_to($orga->getNom(), '@list_parlementaires_organisme?slug='.$orga->getSlug()); ?></h1>
<h2>Présence à la <?php echo $seance->getTitre(1); ?></h2>
<ul>
<?php foreach($presences as $presence) : ?>
<?php $p = $presence->getParlementaire(); ?>
<?php $nbpreuves = $presence->getNbPreuves(); ?>
<li><?php echo link_to($p->nom, '@parlementaire?slug='.$p->getSlug()).', '.$p->getLongStatut(1); ?> <em><a href="<?php echo url_for('@preuve_presence_seance?seance='.$seance->id.'&slug='.$p->slug); ?>">(<?php echo ($nbpreuves>1) ? "$nbpreuves preuves" : "1 preuve"; ?>)</a></em></li>
<?php endforeach; ?>
</ul>
</div>