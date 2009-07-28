<h1><?php echo $parlementaire->nom; ?></h1>
<?php if ($parlementaire->getPhoto()) { echo image_tag($parlementaire->getPhoto(), 'alt=Photo de '.$parlementaire->nom); } ?>
<h2><?php echo $parlementaire->getLongStatut(); ?></h2>
<p>Mandat en cours débuté le <?php echo $parlementaire->debut_mandat ?></p>
<?php if ($parlementaire->site_web) : ?>
<p><a href="<?php echo $parlementaire->site_web ?>">Site web</a></p>
<?php endif; ?>
<p><a href="<?php echo $parlementaire->url_an ?>">Fiche sur le site de l'Assemblée Nationale</a></p>
<p>Profession : 
<?php if ($parlementaire->profession) : ?>
<?php echo link_to($parlementaire->profession, '@list_parlementaires_profession?profession='.$parlementaire->profession); ?>
<?php else : ?>
Non communiquée
<?php endif; ?></p>
<p>Groupe : <?php echo link_to($parlementaire->getGroupe()->getNom(), '@list_parlementaires_organisme?slug='.$parlementaire->getGroupe()->getSlug()); ?> (<?php echo $parlementaire->getGroupe()->getFonction(); ?>)</p>
<p>Responsabilités parlementaires :</p>
<ul>
<?php foreach ($parlementaire->getResponsabilites() as $resp) { ?>
<li><?php echo link_to($resp->getNom(), '@list_parlementaires_organisme?slug='.$resp->getSlug()); ?> (<?php echo $resp->getFonction(); ?>)</li>
<?php } ?>
</ul>
<?php if ($parlementaire->getExtras()) { ?>
<p>Responsabilités extra-parlementaires :</p>
<ul>
<?php foreach ($parlementaire->getExtras() as $extra) { ?>
<li><?php echo link_to($extra->getNom(),'@list_parlementaires_organisme?slug='.$extra->getSlug() ); ?> (<?php echo $extra->getFonction(); ?>)</li>
<?php } ?>
</ul>
<?php } ?>
<p><?php echo link_to("Présences",'@parlementaire_presences?slug='.$parlementaire->getSlug()); ?></p>
<p><?php echo link_to("Interventions",'@parlementaire_interventions?slug='.$parlementaire->getSlug()); ?></p>

