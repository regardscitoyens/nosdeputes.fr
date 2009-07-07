<h1><? echo $parlementaire->nom; ?></h1>
<h2>Député<? if ($parlementaire->sexe == 'F') echo 'e'; ?> de la <? echo $parlementaire->num_circo; if ($parlementaire->num_circo == 1) echo 'ère'; else echo 'ème'; ?> circonscription de <? echo $parlementaire->nom_circo; ?></h2>
<p>Mandat en cours débuté le <? echo $parlementaire->debut_mandat ?></p>
<? if ($parlementaire->site_web) : ?>
<p><a href="<? echo $parlementaire->site_web ?>">Site web</a></p>
<? endif; ?>
<p><a href="<? echo $parlementaire->url_an ?>">Fiche sur le site de l'Assemblée Nationale</a></p>
<p>Profession : <? echo $parlementaire->profession ?></p>