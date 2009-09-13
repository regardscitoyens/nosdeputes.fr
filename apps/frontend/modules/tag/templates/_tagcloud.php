<style>
.tag_level_0 {font-size: 0.8em;}
.tag_level_1 {font-size: 0.9em;}
.tag_level_2 {font-size: 1em;}
.tag_level_3 {font-size: 1.5em;}
</style>
<?php $keys = array_keys($tags);
if (count($keys)) { foreach($keys as $tag) : ?>
<span class="tag_level_<?php echo $tags[$tag]['class']; ?>"><a href="<?php 
echo url_for($route.'tags='.$tags[$tag]['related']); ?>" title="<?php echo $tags[$tag]['count']; ?>"><?php 
$nom = preg_replace('/\s+/', '&nbsp;', $tags[$tag]['tag']);
echo $nom; ?></a> <?php
?></span><?php endforeach;
} else { ?>
<span><em>Désolé, aucun mot-clé pertinent trouvé</em></span>
<?php } ?>