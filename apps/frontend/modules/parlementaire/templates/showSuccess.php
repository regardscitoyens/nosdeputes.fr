<h1><? echo $parlementaire->nom; ?></h1>
<h2>Député<? if ($parlementaire->sexe == 'F') echo 'e'; ?> de la <? echo $parlementaire->num_circo; if ($parlementaire->num_circo == 1) echo 'ère'; else echo 'ème'; ?> circonscription de <? echo $parlementaire->nom_circo; ?></h2>
<p>Mandat en cours débuté le <? echo $parlementaire->debut_mandat ?></p>
<? if ($parlementaire->site_web) : ?>
<p><a href="<? echo $parlementaire->site_web ?>">Site web</a></p>
<? endif; ?>
<p><a href="<? echo $parlementaire->url_an ?>">Fiche sur le site de l'Assemblée Nationale</a></p>
<p>Profession : <? echo link_to($parlementaire->profession, '@list_parlementaires_profession?profession='.$parlementaire->profession); ?></p>
<p>Groupe : <? echo link_to($parlementaire->getGroupe()->getNom(), '@list_parlementaires_organisme?slug='.$parlementaire->getGroupe()->getSlug()); ?> (<? echo $parlementaire->getGroupe()->getFonction(); ?>)</p>
<p>Responsabilités parlementaires :</p>
<ul>
<? foreach ($parlementaire->getResponsabilites() as $resp) { ?>
<li><? echo link_to($resp->getNom(), '@list_parlementaires_organisme?slug='.$resp->getSlug()); ?> (<? echo $resp->getFonction(); ?>)</li>
<? } ?>
</ul>
<p>Responsabilités extra-parlementaires :</p>
<ul>
<? foreach ($parlementaire->getExtras() as $extra) { ?>
<li><? echo link_to($extra->getNom(),'@list_parlementaires_organisme?slug='.$extra->getSlug() ); ?> (<? echo $extra->getFonction(); ?>)</li>
<? } ?>
</ul>