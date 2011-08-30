<?php if (!count($questions)) { ?>
    <i>Ce sénateur n'a posé aucune question $type.</i>
<?php return ;}?>
<ul>
<?php foreach($questions as $question) {
  $titre = $question->shortTitre();
  if ($type === "orale")
    $titre = $question->type." : ".$titre;
  if ($question->nb_commentaires)
    $titre .= ' <span class="list_com">'.$question->nb_commentaires.'&nbsp;commentaire';
  if ($question->nb_commentaires > 1)
    $titre .= 's';
  if ($question->nb_commentaires)
    $titre .= '</span>';
  echo '<li>'.link_to($titre, url_for('@question_numero?numero='.$question->numero.'&legi='.$question->legislature)).'</li>';
} ?>
</ul>
