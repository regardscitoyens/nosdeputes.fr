<div class="internal_tag_cloud">
<?php $keys = array_keys($tags);
if (count($keys)) { foreach($keys as $tag) : ?>
<span class="tag_level_<?php echo $tags[$tag]['class']; ?>"><a href="<?php 
$rel = $tags[$tag]['related'];
//$rel = preg_replace('/\s+/', '&nbsp;', $rel);
$rel = preg_replace('/^aZ/', 'â', $rel);
$rel = preg_replace('/^eZ/', 'é', $rel);
$rel = preg_replace('/^EZ/', 'É', $rel);
$rel = preg_replace('/^iZ/', 'î', $rel);
$rel = preg_replace('/^IZ/', 'Î', $rel);
echo url_for($route.'tags='.$rel); ?>" title="<?php echo $tags[$tag]['count']; ?>"><?php 
$nom = preg_replace('/\s+/', '&nbsp;', $tags[$tag]['tag']);
$nom = preg_replace('/^aZ/', 'â', $nom);
$nom = preg_replace('/^eZ/', 'é', $nom);
$nom = preg_replace('/^EZ/', 'É', $nom);
$nom = preg_replace('/^iZ/', 'î', $nom);
$nom = preg_replace('/^IZ/', 'Î', $nom);
echo $nom; ?></a> <?php
		 ?></span><?php endforeach; 
if(isset($hide) && $hide) 
  echo '<span class="hide">finfinfinfinfinfinfinfinfinfinfinfinfinfinfinf</span>';
 } else { ?>
<span><em>Désolé, aucun mot-clé pertinent trouvé</em></span>
<?php } ?></div>
