<?php

echo '<tr class="alinea';
if (isset($alinea) && $a->numero == $alinea->numero)
	echo '_select';
else 
	$a->texte = preg_replace('/\<\/?b\>/', '', $a->texte);
echo '" id="alinea_'.$a->texteloi_id.'-'.$slug_article.'-'.$a->numero.'"><td class="alineanumero"><p>'.$a->numero.'.</p></td><td class="alineatexte">';
$s = $a->texte;
$s = preg_replace('/(articles? (L?)\.? ?([0-9.\-a-z]+)) (de la loi (n° *[\d\-]+ du \d+e?r? \S+ \d{4}))/i', '<a href="/redirect/loi/\\5/\\2\\3">\\1</a> \\4', $s);
$s = preg_replace('/(articles? (L?)\.? ?([0-9.\-a-z]+ ?[A-Z]?)) (du code ([^,.»]+))([,.»]| est| et au| sont)/i', '<a href="/redirect/loi/\\5/\\2\\3">\\1</a> \\4\\6', $s);
$s = preg_replace('/(articles? (L?)\.? ?([0-9.\-]+))/i', '<a href="/redirect/loi/'.preg_replace('/^(code|livre) */', '', $a->ref_loi).'/\\2\\3">\\1</a>', $s);
$s = preg_replace('/(loi (n° *[\d\-]+ du \d+e?r? \S+ \d{4}))([^"\/])/', '<a href="/redirect/loi/\\2">\\1</a>\\3', $s);
if ($a->ref_loi) 
$s = preg_replace('/('.$a->ref_loi.')([^"\/])/', '<a href="/redirect/loi/\\1">\\1</a>\\2', $s);
$s = preg_replace('/\s+(:|;|!|\?|»|\-)/', '&nbsp;\1', $s);
$s = preg_replace('/(«|\-)\s+/', '\1&nbsp;', $s);
echo $s;
?><div class="commentaires" id='com_<?php echo $a->id; ?>'><span class="link_comment">&nbsp;</span><span class="com_link" id="com_link_<?php echo $a->id; ?>"><a href="<?php echo url_for('@loi_alinea?id='.$a->id); ?>#ecrire">Voir tous les commentaires - Laisser un commentaire</a></span></div>
</td></tr>
