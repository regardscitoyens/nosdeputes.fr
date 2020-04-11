<?php use_helper('Text') ?>
<div class="loi">
<h1><?php echo link_to($loi->titre, '@loi?loi='.$loi->texteloi_id); ?></h1>
<h2><?php if (isset($section)) echo '<a href="'.url_for('@loi_chapitre?loi='.$loi->texteloi_id.'&chapitre='.$chapitre->chapitre).'">'; 
echo 'Chapitre '.$chapitre->chapitre.'&nbsp;: '.$chapitre->titre;
if (isset($section)) echo '</a>'; ?></h2>
<?php $expose = '';
if (isset($section)) {
  echo '<h3>Section '.$section->section.'&nbsp;: '.$section->titre.'</h3>';
  echo '<div class="pagerloi">';
  if (isset($precedent)) {
    echo '<div class="precedent">'.link_to('Section '.$precedent, '@loi_section?loi='.$loi->texteloi_id.'&chapitre='.$chapitre->chapitre.'&section='.$precedent).'</div>';
  }
  if (isset($suivant))
    echo '<div class="suivant">'.link_to('Section '.$suivant, '@loi_section?loi='.$loi->texteloi_id.'&chapitre='.$chapitre->chapitre.'&section='.$suivant).'</div>';
  echo '</div>';
  if (isset($section->expose)) {
    $expose = $section->expose;
    echo myTools::escape_blanks($expose);
  }
} else {
  echo '<div class="pagerloi">';
  if (isset($precedent)) {
    echo '<div class="precedent">'.link_to('Chapitre '.$precedent, '@loi_chapitre?loi='.$loi->texteloi_id.'&chapitre='.$precedent).'</div>';
  }
  if (isset($suivant))
    echo '<div class="suivant">'.link_to('Chapitre '.$suivant, '@loi_chapitre?loi='.$loi->texteloi_id.'&chapitre='.$suivant).'</div>';
  echo '</div>';
  if (isset($chapitre->expose)) {
    $expose = $chapitre->expose;
    echo myTools::escape_blanks($expose);
  } 
}
if (isset($soussections)) {
  $sections = array();
  foreach ($soussections as $ss) {
    $sections[$ss->id] = array('numero' => $ss->section, 'titre' => $ss->titre, 'expose' => $ss->expose);
  }
  unset($soussections);
}

$nart = 0;
$changesec = 0;
if (isset($sections)) $nsec = 0;
foreach ($articles as $a) {
  if (isset($sections) && isset($sections[$a->titre_loi_id])) {
    $section = $sections[$a->titre_loi_id];
    if ($section['numero'] != $nsec) {
      if ($nsec != 0) echo '</ul></li>';
      else echo '<ul>';
      $nsec = $section['numero'];
      $changesec = 1;
      echo '<li><b><a href="'.url_for('@loi_section?loi='.$loi->texteloi_id.'&chapitre='.$chapitre->chapitre.'&section='.$nsec).'">';
      echo 'Section '.$nsec.'&nbsp;: '.$section['titre'];
      if (isset($section['expose']) && $section['expose'] != "") {
        $expose = myTools::escape_blanks(truncate_text(html_entity_decode(strip_tags($section['expose']), ENT_NOQUOTES, "UTF-8"), 250));
        echo '</b><blockquote>'.$expose.'</blockquote></a>';
      } else echo '</b></a>';
    }
  }
  if ($nart != 0 && $changesec == 0) echo '</li>';
  else {
    echo '<ul>';
    $changesec = 0;
  }
  $atitre = strtolower($a->titre);
  if (isset($amendements['avant '.$atitre])) {
    echo '<li><b>Amendement';
    if (count($amendements['avant '.$atitre]) > 1) echo 's';
    echo ' proposant un article additionel avant l\'article '.$a->titre.'&nbsp;:</b> ';
    foreach ($amendements['avant '.$atitre] as $adt) echo link_to('n°&nbsp;'.$adt, '@amendement?loi='.$loi->texteloi_id.'&numero='.preg_replace('/^([A-Z]{1,3})?(\d+)\s+.*$/', '\1\2', $adt)).' ';
    echo '</li>';
  }
  $nart = $a->ordre;
  echo '<li><a href="'.url_for('@loi_article?loi='.$loi->texteloi_id.'&article='.$a->slug).'">';
  echo '<b>Article '.$a->titre.'</b></a>';
  if ($a->nb_commentaires > 0 || isset($amendements[$atitre])) echo ' (';
  if ($a->nb_commentaires > 0) {
    echo '<span class="coms_loi_txt">'.$a->nb_commentaires.' commentaire';
    if ($a->nb_commentaires > 1) echo 's';
	echo '</span>';
  }
  if ($a->nb_commentaires > 0 && isset($amendements[$atitre])) echo ', ';
  if (isset($amendements[$atitre])) {
    $ct = count($amendements[$atitre]);
    echo $ct.' amendement';
    if ($ct > 1) echo 's';
    echo '&nbsp;: ';
    foreach ($amendements[$atitre] as $adt) echo link_to('n°&nbsp;'.$adt, '@amendement?loi='.$loi->texteloi_id.'&numero='.preg_replace('/^([A-Z]{1,3})?(\d+)\s+.*$/', '\1\2', $adt)).' ';
      echo '<a href="'.url_for('@loi_article?loi='.$loi->texteloi_id.'&article='.$a->slug).'">';
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
    if (count($amendements['après '.$atitre]) > 1) echo 's'; 
    echo ' proposant un article additionel après l\'article '.$a->titre.'&nbsp;:</b> ';
    foreach ($amendements['après '.$atitre] as $adt) echo link_to('n°&nbsp;'.$adt, '@amendement?loi='.$loi->texteloi_id.'&numero='.preg_replace('/^([A-Z]{1,3})?(\d+)\s+.*$/', '\1\2', $adt)).' ';
  }
} 
if ($nart != 0) echo '</ul>'; ?>
</div>
