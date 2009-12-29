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
  if ($section->section > 1) {
    $precedent = $section->section - 1;
    echo '<div class="precedent">'.link_to('Section '.$precedent, '@loi_section?loi='.$loi->texteloi_id.'&chapitre='.$chapitre->chapitre.'&section='.$precedent).'</div>';
  }
  if (isset($suivant))
    echo '<div class="suivant">'.link_to('Section '.$suivant, '@loi_section?loi='.$loi->texteloi_id.'&chapitre='.$chapitre->chapitre.'&section='.$suivant).'</div>';
  echo '</div>';
  if (isset($section->expose)) {
    $expose = $section->expose;
    echo $expose;
  }
} else {
  echo '<div class="pagerloi">';
  if ($chapitre->chapitre > 1) {
    $precedent = $chapitre->chapitre - 1;
    echo '<div class="precedent">'.link_to('Chapitre '.$precedent, '@loi_chapitre?loi='.$loi->texteloi_id.'&chapitre='.$precedent).'</div>';
  }
  if (isset($suivant))
    echo '<div class="suivant">'.link_to('Chapitre '.$suivant, '@loi_chapitre?loi='.$loi->texteloi_id.'&chapitre='.$suivant).'</div>';
  echo '</div>';
  if (isset($chapitre->expose)) {
    $expose = $chapitre->expose;
    echo $expose;
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
        $expose = truncate_text(preg_replace('/<\/?p>|\&[^\;]+\;/i', ' ', $section['expose']), 250);
        echo '</b><blockquote>'.$expose.'</blockquote></a>';
      } else echo '</b></a>';
    }
  }
  if ($nart != 0 && $changesec == 0) echo '</li>';
  else {
    echo '<ul>';
    $changesec = 0;
  }
  $nart = $a->ordre;
  echo '<li><a href="'.url_for('@loi_article?loi='.$loi->texteloi_id.'&article='.$a->slug).'">';
  echo '<b>Article '.$a->titre;
  if ($a->nb_commentaires > 0) {
    echo ' ('.$a->nb_commentaires.' commentaire';
      if ($a->nb_commentaires > 1) echo 's';
    echo ')';
  }
  if (isset($a->expose) && $a->expose != "") {
    $tmpexpo = truncate_text(preg_replace('/<\/?p>|\&[^\;]+\;/i', ' ', $a->expose), 250);
    if ($expose == '' || !(truncate_text($expose, 200) === truncate_text($tmpexpo, 200))) {
      $expose = $tmpexpo;
      echo '&nbsp;:</b><blockquote>'.$expose.'</blockquote>';
    } else echo '</b>';
  } else echo '</b>';
  echo '</a>';
} 
if ($nart != 0) echo '</ul>'; ?>
</div>
