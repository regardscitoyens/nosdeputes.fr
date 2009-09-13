<ul>
<?php $cpt = 0; foreach($questions as $question) :
$cpt ++;
$titre = myTools::displayDate($question->date).'&nbsp;: Ministère d';
$ministre = preg_replace('/^.*\/\s*([\wàéëêèïîôöûüÉ]+)$/', '\\1', $question->ministere);
$ministre = preg_replace('/^([\wàéëêèïîôöûüÉ]+)[,\s].*$/', '\\1', $question->ministere);
if (preg_match('/^[AEÉIOU]/', $ministre)) $titre .= 'e l\'';
else $titre .= 'u ';
$titre .= $ministre.'&nbsp;(';
$theme = preg_replace('/^\s*([\wàéëêèïîôöûüÉ\s]+)*[,\/].*$/', '\\1', $question->themes);
$theme = preg_replace('/^(.*)\s+$/', '\\1', $theme);
$titre .= $theme.')';
?>
  <li><?php echo link_to($titre, url_for('@question?id='.$question->id)); ?></li>
<?php if (isset($limit) && $cpt >= $limit) break; endforeach; ?>
</ul>