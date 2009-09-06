<ul>
<?php $cpt = 0; foreach($textes as $texte) : 
if (preg_match('/questions?\s/', $texte['Section']['titre'])) continue;
$cpt ++;
?>
<li><?php echo link_to($texte['Section']['titre'].' ('.$texte['nb'].' interventions)',
		       '@parlementaire_texte?slug='.$parlementaire->slug.'&id='.$texte['section_id']); ?></li>
<?php if (isset($limit) && $cpt >= $limit) break; endforeach; ?>
</ul>
