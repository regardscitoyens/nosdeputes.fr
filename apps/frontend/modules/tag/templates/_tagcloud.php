<?php $abs = '';
if (isset($absolute) && $absolute)
  $abs = 'absolute=true';
if (!isset($target))
  $target = '';
$keys = array_keys($tags);
$tot = count($keys);
if (!isset($nozerodisplay))
  $nozerodisplay = false;
if (!$nozerodisplay || $tot > 0) {
if ($nozerodisplay) { ?>
<div class="nuage_de_tags nuage_discret">
<h3>Mots-clés</h3>
<?php } ?>
<div class="internal_tag_cloud">
<?php $ct = 1; $keys = array_keys($tags);
if ($tot > 0) { foreach($keys as $tag) : ?>
<?php if (isset($model) && $model == "Texteloi" && $ct % 2 == 0) echo '<br/>'; ?>
<span class="tag_level_<?php echo $tags[$tag]['class']; ?>">
<a<?php echo $target; ?> href="<?php 
$rel = $tags[$tag]['related'];
$rel = preg_replace('/^aZ/', 'â', $rel);
$rel = preg_replace('/^eZ/', 'é', $rel);
$rel = preg_replace('/^EZ/', 'É', $rel);
$rel = preg_replace('/^iZ/', 'î', $rel);
$rel = preg_replace('/^IZ/', 'Î', $rel);
if (isset($route)) echo url_for($route.'tags='.$rel, $abs); 
else {
  if (!isset($extraroute)) $extraroute = '';
  echo url_for('@recherche_solr?query="'.myTools::solrize($rel).'"&sort=1'.$extraroute, $abs);
} ?>" title="<?php echo $tags[$tag]['count']; ?>"><?php
$nom = preg_replace('/^aZ/', 'â', $tags[$tag]['tag']);
$nom = preg_replace('/^eZ/', 'é', $nom);
$nom = preg_replace('/^EZ/', 'É', $nom);
$nom = preg_replace('/^iZ/', 'î', $nom);
$nom = preg_replace('/^IZ/', 'Î', $nom);
if (!isset($absolute) || !$absolute)
  $nom = preg_replace('/\s+/', '&nbsp;', $nom);
echo $nom; ?>
</a></span> <?php $ct++; endforeach; 
} else { ?>
<span><em>Aucun mot-clé trouvé</em></span>
<?php } ?>
</div>
<?php if ($nozerodisplay) { ?>
</div>
<?php }
} ?>
