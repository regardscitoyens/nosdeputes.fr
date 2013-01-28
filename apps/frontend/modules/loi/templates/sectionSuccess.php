<?php use_helper('Text') ?>
<div class="loi">
<h1><?php echo link_to($loi->titre, '@loi?loi='.$loi->texteloi_id); ?></h1>
<h2><?php echo $titre; ?></h2>
<?php $expose = '';
echo '<div class="pagerloi">';
$baseurl = '@loi_level'.$level.'?loi='.$loi->texteloi_id;
for ($i = 1; $i < $level; $i++)
  $baseurl .= "&level".$i."=".$section->getLevelValue($i);
if ($voisins[0]) echo '<div class="precedent">'.link_to(ucfirst($section->leveltype).' '.$voisins[0], $baseurl."&level".$level.'='.$voisins[0]).'</div>';
if ($voisins[1]) echo '<div class="suivant">'.link_to(ucfirst($section->leveltype).' '.$voisins[1], $baseurl."&level".$level.'='.$voisins[1]).'</div>';
echo '</div>';
if (isset($section->expose)) {
  $expose = $section->expose;
  echo myTools::escape_blanks($expose);
} ?>
<div class="sommaireloi">
<?php if (isset($soussections)) {
  $idx_sec = array(); $ct = 0;
  foreach ($soussections as $ss) {
    $idx_sec[$ss->id] = $ct;
    $ct++;
  }
}
$nart = 0;
$changesec = 0;
$cursec = 0;
$nsec = 0;
$level = 0;
foreach ($articles as $a) {
  if (isset($soussections) && isset($idx_sec[$a->titre_loi_id]) && $soussections[$idx_sec[$a->titre_loi_id]]->id != $cursec) {
    $section = $soussections[$idx_sec[$a->titre_loi_id]];
    $cursec = $section->id;
    $changesec = 1;
    echo "</ul>";
    if ($section->level < $level) {
      echo "</li>";
      for ($i=1; $i < $level-$section->level+1; $i++)
        echo "</ul></li>";
    } elseif ($section->level == $level)
      echo "</li>";
    for ($i = $nsec; $i <= $idx_sec[$cursec]; $i++) {
      $parsec = $soussections[$i];
      if ($parsec->level > $level) {
        echo "<ul>";
      }
      $level = $parsec->level;
      echo '<li class="level'.$parsec->level.'"><a href="'.url_for($parsec->getUrl()).'">'.$parsec->getLevelTitre();
      if (isset($parsec->expose) && $parsec->expose != "") {
        $expose = myTools::escape_blanks(truncate_text(html_entity_decode(strip_tags($section['expose']), ENT_NOQUOTES, "UTF-8"), 250));
        echo '</b><blockquote>'.$expose.'</blockquote>';
      }
      echo '</a>';
    }
    $nsec = $idx_sec[$cursec]+1;
  }
  if ($nart != 0 && $changesec == 0) echo '</li>';
  else {
    echo "<ul>";
    $changesec = 0;
  }
  $atitre = strtolower($a->titre);
  if (isset($amendements['avant '.$atitre])) {
    echo '<li><b>Amendement';
    if ($amendements['avant '.$atitre.'tot'] > 1) echo 's';
    echo ' proposant un article additionel avant l\'article '.$a->titre.'&nbsp;:</b> <span class="orange">';
    foreach ($amendements['avant '.$atitre] as $adt) echo link_to('n°&nbsp;'.$adt, '@amendement?loi='.$loi->texteloi_id.'&numero='.preg_replace('/^([A-Z]{1,3})?(\d+)\s+.*$/', '\1\2', $adt)).' ';
    echo '</span></li>';
  }
  $nart = $a->ordre;
  echo '<li class="articleloi"><a href="'.url_for('@loi_article?loi='.$loi->texteloi_id.'&article='.$a->slug).'"><u>Article '.$a->titre.'</u></a>';
  if ($a->nb_commentaires > 0 || isset($amendements[$atitre])) echo ' (';
  if ($a->nb_commentaires > 0) {
    echo '<span class="coms_loi_txt">'.$a->nb_commentaires.' commentaire';
    if ($a->nb_commentaires > 1) echo 's';
	echo '</span>';
  }
  if ($a->nb_commentaires > 0 && isset($amendements[$atitre])) echo ', ';
  if (isset($amendements[$atitre])) {
    $ct = $amendements[$atitre.'tot'];
    echo $ct.' amendement';
    if ($ct > 1) echo 's';
    echo '&nbsp;: <span class="orange">';
    foreach ($amendements[$atitre] as $adt) echo link_to('n°&nbsp;'.$adt, '@amendement?loi='.$loi->texteloi_id.'&numero='.preg_replace('/^([A-Z]{1,3})?(\d+)\s+.*$/', '\1\2', $adt)).' ';
      echo '<a href="'.url_for('@loi_article?loi='.$loi->texteloi_id.'&article='.$a->slug).'"></span>';
  }
  if ($a->nb_commentaires > 0 || isset($amendements[$atitre])) echo ')';
  if (isset($a->expose) && $a->expose != "") {
    $tmpexpo = truncate_text(html_entity_decode(strip_tags($a->expose), ENT_NOQUOTES, "UTF-8"), 250);
    if ($expose == '' || !(truncate_text($expose, 200) === truncate_text($tmpexpo, 200))) {
      $expose = myTools::escape_blanks($tmpexpo);
      echo '<a href="'.url_for('@loi_article?loi='.$loi->texteloi_id.'&article='.$a->slug).'"><blockquote>'.$expose.'</blockquote></a>';
    }
  }
  if (isset($amendements['après '.$atitre])) {
    echo '</li><li><b>Amendement';
    if ($amendements['après '.$atitre.'tot'] > 1) echo 's'; 
    echo ' proposant un article additionel après l\'article '.$a->titre.'&nbsp;:</b> <span class="orange">';
    foreach ($amendements['après '.$atitre] as $adt) echo link_to('n°&nbsp;'.$adt, '@amendement?loi='.$loi->texteloi_id.'&numero='.preg_replace('/^([A-Z]{1,3})?(\d+)\s+.*$/', '\1\2', $adt)).' ';
    echo '</span>';
  }
} 
if ($nart != 0) echo '</ul>';
for ($i = 0; $i < $level; $i++)
  echo "</li></ul>";  ?>
</div>
</div>
