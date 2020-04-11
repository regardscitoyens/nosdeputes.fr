<?php if (!count($questions)) { ?>
    <i>Ce député n'a posé aucune question écrite.</i>
<?php return ;}?>
<ul>
<?php foreach($questions as $question) {
  $titre = myTools::displayVeryShortDate($question->date).'&nbsp;: '.$question->uniqueMinistere();
  if ($theme = $question->firstTheme())
    $titre .= '&nbsp;('.$theme.')';
  if ($question->nb_commentaires)
    $titre .= ' <span class="list_com">'.$question->nb_commentaires.'&nbsp;commentaire';
  if ($question->nb_commentaires > 1)
    $titre .= 's';
  if ($question->nb_commentaires)
    $titre .= '</span>';
  echo '<li>'.link_to($titre, url_for('@question_numero?numero='.$question->numero)).'</li>';
} ?>
</ul>
