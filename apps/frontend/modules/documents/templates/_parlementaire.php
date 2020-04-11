<?php use_helper('Text') ?>
<?php if (!count($docs)) { ?>
    <i>Ce député n'a déposé aucun<?php echo $feminin; ?> <?php echo $type; ?>.</i>
<?php return ;}?>
<ul>
<?php foreach($docs as $doc) {
  $titre = $doc->getTitre();
  $titre = preg_replace('/N°\s\d+/', '', $titre);
  $titre = myTools::displayVeryShortDate($doc->date).'&nbsp;: '.truncate_text($titre, 100);
  if ($doc->nb_commentaires)
    $titre .= ' (<span class="list_com">'.$doc->nb_commentaires.'&nbsp;commentaire';
  if ($doc->nb_commentaires > 1)
    $titre .= 's';
  if ($doc->nb_commentaires)
    $titre .= '</span>)';
  echo '<li>'.link_to($titre, url_for('@document?id='.$doc->id)).'</li>';
} ?>
</ul>
