<?php if ($dossier) echo '<div class="source">'.link_to('Dossier relatif', '@section?id='.$dossier)."</div>"; ?>
<div class="loi"><h1><?php echo $loi->titre; ?></h1></div>
<?php if ($loi->source) echo '<p class="source"><a href="'.$loi->source.'" rel="nofollow">Source</a></p><div class="clear"></div>';
if ($loi->parlementaire_id && $loi->expose && !($loi->texteloi_id == 2760)) { ?>
  <div class="loi"><div class="intervenant">
  <?php $perso = $loi->getParlementaire();
  if ($perso->getPageLink() && $photo = $perso->hasPhoto()) {
    echo '<a href="'.url_for($perso->getPageLink()).'">';
    include_partial('parlementaire/photoParlementaire', array('parlementaire' => $perso, 'height' => 70));
    echo '</a>';
  }
  echo '</div>';
  echo myTools::escape_blanks($loi->expose);
  echo '<div class="auteurloi"><a href="'.url_for($perso->getPageLink()).'">'.$perso->nom.'</a></div></div><br/>';
} ?>
<div class="sommaireloi">
<?php $nart = 0; $nbart = 0;
if (isset($soussections)) {
  $chapitre = 0;
  $section = 0;
  foreach ($soussections as $ss) {
    if (($section != 0 || $chapitre != 0) && ($ss->chapitre != $chapitre || $ss->section > 1)) {
      echo '<br/><small> &nbsp; Article';
      if ($nbart > 1) echo 's';
      echo '&nbsp;: ';
      for ($i=$nart;$i<$nart+$nbart;$i++) {
        echo link_to($articles[$i]['titre'], '@loi_article?loi='.$loi->texteloi_id.'&article='.$articles[$i]['slug']);
        if ($i != $nart+$nbart-1) echo ', ';
      }
      $nart += $nbart;
      echo '</small>';
    }
    $nbart = $ss->nb_articles;
    if (isset($ss->chapitre) && $ss->chapitre != $chapitre && (!($ss->section) || $ss->section == 0)) {
      if ($section != 0) echo '</li></ul>';
      $section = 0;
      if ($chapitre != 0) echo '</li>';
      else echo '<ul>';
      $chapitre = $ss->chapitre;
      echo '<li><b><a href="'.url_for('@loi_chapitre?loi='.$loi->texteloi_id.'&chapitre='.$chapitre).'">';
      echo 'Chapitre '.$chapitre;
    } else if (isset($ss->section) && $ss->section != $section) {
      if ($section != 0) echo '</li>';
      else echo '<ul>';
      $section = $ss->section;
      echo '<li><a href="'.url_for('@loi_section?loi='.$loi->texteloi_id.'&chapitre='.$chapitre.'&section='.$section).'">';
      echo 'Section '.$section;
    }
    echo '&nbsp;: '.$ss->titre.'</a>';
    if ($section == 0) echo '</b>';
    echo ' ('.$ss->nb_articles.' article';
    if ($ss->nb_articles > 1) echo 's';
    if ($ss->nb_commentaires > 0) {
      echo ', <span class="coms_loi_txt">'.$ss->nb_commentaires.' commentaire';
      if ($ss->nb_commentaires > 1) echo 's';
    }
    echo '</span>)';
  }
  echo '<br/><small> &nbsp; Article';
  if ($nbart > 1) echo 's';
  echo '&nbsp;: ';
  for ($i=$nart;$i<$nart+$nbart;$i++) {
    echo link_to($articles[$i]['titre'], '@loi_article?loi='.$loi->texteloi_id.'&article='.$articles[$i]['slug']);
    if ($i != $nart+$nbart-1) echo ', ';
  }
  echo '</small>';
  if ($section != 0) echo '</li></ul>';
  if ($chapitre != 0) echo '</li></ul>';
  if ($amendements) {
    echo '<p class="suivant">'.link_to('Voir les '.$amendements.' amendements déposés sur ce texte', '@find_amendements_by_loi_and_numero?loi='.$loi->texteloi_id.'&numero=all');
    if (file_exists('liasses/liasse_'.$loi->texteloi_id.'.pdf')) echo '<br/>(<a href="/liasses/liasse_'.$loi->texteloi_id.'.pdf">version imprimable</a>)';
    echo '</p>';
  }
} else {
  foreach ($articles as $a) {
    if ($nart != 0) echo '</li>';
    else echo '<ul>';
    $nart = $a->ordre;
    echo '<li><a href="'.url_for('@loi_article?loi='.$loi->texteloi_id.'&article='.$a->slug).'">';
    echo 'Article '.$a->titre;
    if (isset($a->expose)) echo '&nbsp;:'.myTools::escape_blanks(truncate_text(preg_replace('/<\/?p>|\&[^\;]+\;/i', ' ', $a->expose), 120));
    echo '</a>';
  }
} ?>
</div>
<br/>
<?php if ((!$loi->parlementaire_id && $loi->expose) || $loi->texteloi_id == 2760) {
  echo '<div class="loi"><h2>Exposé des motifs&nbsp;:</h2>';
  if ($loi->parlementaire_id && $perso = $loi->getParlementaire()) 
    if ($perso->getPageLink() && $photo = $perso->hasPhoto()) {
      echo '<div class="intervenant"><a href="'.url_for($perso->getPageLink()).'">';
      include_partial('parlementaire/photoParlementaire', array('parlementaire' => $perso, 'height' => 70));
      echo '</a></div>';
    }
  echo myTools::escape_blanks($loi->expose).'</div><br/>';
} ?>
<div class="commentaires">
  <h3>Derniers commentaires sur <?php echo preg_replace('/<br\/>.*$/', '', $loi->titre); ?> <span class="rss"><a href="<?php echo url_for('@loi_rss_commentaires?loi='.$loi->texteloi_id); ?>"><?php echo image_tag('xneth/rss.png', 'alt="Flux rss"'); ?></a></span></h3>
<?php if ($loi->nb_commentaires == 0) echo '<p>Cette loi n\'a pas encore inspiré de commentaire aux utilisateurs.</p>';
else {
  echo include_component('commentaire', 'lastObject', array('object' => $loi, 'presentation' => 'noloi'));
  if ($loi->nb_commentaires > 4)
    echo '<p class="suivant">'.link_to('Voir les '.$loi->nb_commentaires.' commentaires', '@loi_commentaires?loi='.$loi->texteloi_id).'</p><div class="stopfloat"></div>';
} ?>
</div>

