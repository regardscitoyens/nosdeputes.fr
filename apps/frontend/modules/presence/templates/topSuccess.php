<h1>Top des présences</h1>
<?$cpt = 0 ; foreach ($top as $t) : $cpt++;?>
<p><? echo $cpt; ?> - <? echo link_to($t['nom'], '@parlementaire?slug='.$t['slug']); ?> (<? echo link_to($t['nb']." presence(s)", '@parlementaire_presences?slug='.$t['slug']); ?>)</p>
<?endforeach; ?>