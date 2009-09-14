<?php
$titre = 'Questions écrites';
echo include_component('parlementaire', 'header', array('parlementaire' => $parlementaire, 'titre' => $titre));
?>
<div class="questions">
<?php 
  echo include_component('questions', 'pagerQuestions', array('question_query' => $questions, 'mots'=>''));


if(count($questions) < 1) : ?>
Ce député n'a posé aucune question.
<?php else : ?>
<?php foreach($questions as $question) : ?>
<tr>
<td><?php echo link_to($question->numero, '@question?id=' . $question->id) ?></td>
<td><?php echo $question->ministere ?></td>
<td><?php echo $question->themes ?></td>
</tr>
<?php endforeach ?>
</table>
<?php endif ?>
</div>
