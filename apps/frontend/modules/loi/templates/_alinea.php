<?php

echo '<tr class="alinea';
if (isset($alinea) && $a->numero == $alinea->numero)
	echo '_select';
else 
	$a->texte = preg_replace('/\<\/?b\>/', '', $a->texte);
	$a->texte = preg_replace('/^\<p(>\<i\>«.*\<\/i\>\<\/p\>)$/', '<p class="center"\\1</div>', $a->texte);
echo '" id="alinea_'.$a->numero.'"><td class="alineanumero"><a href="#alinea_'.$a->numero.'">'.$a->numero.'.</a></td><td class="alineatexte">';
$s = $a->texte;
$s = preg_replace('/(articles? ?([0-9.\-a-z]+)) (de la )(constitution)/i', '<a href="/redirect/loi/constitution/\\2">\\1</a> \\3<a href="/redirect/loi/constitution">\\4</a>', $s);
$s = preg_replace('/(article ?([0-9]+ ?[a-z]* ?[A-Z]*)) (de la présente loi)/', '<a href="/'.sfConfig::get('app_legislature').'/loi/'.$a->texteloi_id.'/article/\\2">\\1</a> \\3', $s);
$s = preg_replace('/(article ?([0-9]+ ?[a-z]* ?[A-Z]*))\./', '<a href="/'.sfConfig::get('app_legislature').'/loi/'.$a->texteloi_id.'/article/\\2">\\1</a>.', $s);
$s = preg_replace('/(articles? ([ADL]?)\.*(O)?\.? ?([0-9.\-a-z]+)) (de la loi (n° *[\d\-]+ du \d+e?r? \S+ \d{4}))/i', '<a href="/redirect/loi/\\6/\\2\\3\\4">\\1</a> \\5', $s);
$s = preg_replace('/(articles? ([ADL]?)\.*(O)?\.? ?([0-9.\-a-z]+)( <i>([^<]*)<\/i>)?) (de l[^ ]*(ordonnance n° *[\d\-]+ du \d+e?r? \S+ \d{4}))/i', '<a href="/redirect/loi/\\8/\\2\\3\\4\\6">\\1</a> \\7', $s);
$s = preg_replace('/(articles? ([ADL]?)\.*(O)?\.? ?([0-9.\-a-z]+ ?[A-Z]?)) (à|et) (([ADL]?)\.*(O)?\.? ?([0-9.\-a-z]+ ?[A-Z]?)) (du (livre|code) ([^,.»]+))([,.»]| est| et au| sont)/i', '<a href="/redirect/loi/\\12/\\2\\3\\4">\\1</a> \\5 <a href="/redirect/loi/\\12/\\7\\8\\9">\\6</a> \\10\\13', $s);
$s = preg_replace('/(articles? ([ADL]?)\.*(O)?\.? ?([0-9.\-a-z]+ ?[A-Z]?)) (du (code|livre) ([^,.»]+))([,.»]| est| et au| sont)/i', '<a href="/redirect/loi/\\7/\\2\\3\\4">\\1</a> \\5\\8', $s);
if ($a->ref_loi) {
  $s = preg_replace('/([\' ])(articles? ([ADL]?)\.*(O)?\.? ?(\d[0-9.\-a-z]+ ?[A-Z]?))( [^<]?)/i', '\\1<a href="/redirect/loi/'.preg_replace('/^(code|livre) */', '', $a->ref_loi).'/\\3\\4\\5">\\2</a>\\6', $s);
  $s = preg_replace('/([^.\ses]\s+)(([ADL])\.*(O)?\.? ?(\d[0-9.\-a-z]+ ?[A-Z]?)) ([^<])/i', '\\1<a href="/redirect/loi/'.preg_replace('/^(code|livre) */', '', $a->ref_loi).'/\\3\\4\\5">\\2</a> \\6', $s);
}
$s = preg_replace('/ (loi (organique )?(n° *[\d\-]+ du \d+e?r? \S+ \d{4}))([^"\/])/', ' <a href="/redirect/loi/\\2\\3">\\1</a>\\4', $s);
$s = preg_replace('/ (loi (organique )?(n° *[\d\-]+))([^"\/])/', ' <a href="/redirect/loi/\\2\\3">\\1</a>\\4', $s);
$s = preg_replace('/(l[^ \/]*)(ordonnance (n° *[\d\-]+ du \d+e?r? \S+ \d{4}))/i', '\\1<a href="/redirect/loi/\\2">\\2</a>', $s);
$s = preg_replace('/(articles? ?([0-9]+ ?[a-z]* ?[A-Z]*))(, | ;| est| se | établi| sont| et | le)/', '<a href="/'.sfConfig::get('app_legislature').'/loi/'.$a->texteloi_id.'/article/\\2">\\1</a>\\3', $s);
if ($a->ref_loi) 
  $s = preg_replace('/('.$a->ref_loi.')([^"\/])/', '<a href="/redirect/loi/\\1">\\1</a>\\2', $s);
$s = preg_replace('/\s+(:|;|!|\?|»|\-)/', '&nbsp;\1', $s);
$s = preg_replace('/(«|\-)\s+/', '\1&nbsp;', $s);
$s = preg_replace('/\<a href="([^"]+)\.">/', '<a href="\1">', $s);
if (isset($amendements)) {
  $string = '<br/><small>';
  $string .= $totalamdmts;
  $string .= ' amendement';
  if ($totalamdmts > 1) $string .= 's';
  $string .= ' déposé';
  if ($totalamdmts > 1) $string .= 's';
  $string .= ' sur cet alinéa&nbsp;: <span class="orange">';
  foreach ($amendements as $adt) $string .= link_to('n°&nbsp;'.$adt, '@amendement?loi='.$loi.'&numero='.preg_replace('/^([A-Z]{1,3})?(\d+)\s+.*$/', '\1\2', $adt)).' ';
  $string .= '</span></small></p>';
  $s = preg_replace('/<\/p>$/', $string.'</p>', $s);
}
echo myTools::escape_blanks($s);
?>
<div class="commentaires" id='com_<?php echo $a->id; ?>'>
<span class="link_comment list_com">&nbsp;</span><span class="com_link list_com" id="com_link_<?php echo $a->id; ?>"><a href="<?php echo url_for('@loi_alinea?id='.$a->id); ?>#ecrire">Voir tous les commentaires - Laisser un commentaire</a></span></div>
</td></tr>
