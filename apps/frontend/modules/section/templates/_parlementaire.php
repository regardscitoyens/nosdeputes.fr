<ul>
<?php $cpt = 0; foreach($textes as $texte) : 
if (preg_match('/questions?\s/', $texte['Section']['titre'])) continue;
$cpt ++;
?>
<li><?php echo link_to(ucfirst(preg_replace('/\s*\?$/', '', $texte['Section']['titre'])).' ('.$texte['nb'].'&nbsp;interventions)',
		       '@parlementaire_texte?slug='.$parlementaire->slug.'&id='.$texte['section_id']); ?></li>
<?php if (isset($limit) && $cpt >= $limit) break; endforeach; ?>
</ul>
