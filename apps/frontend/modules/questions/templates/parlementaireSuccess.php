<?php
$titre = 'Questions écrites';
$sf_response->setTitle('Questions écrite de '.$parlementaire->nom);
echo include_component('parlementaire', 'header', array('parlementaire' => $parlementaire, 'titre' => $titre, 'rss' => '@parlementaire_questions_rss?slug='.$parlementaire->slug));
?>
<div class="questions">
<?php echo include_component('questions', 'pagerQuestions', array('question_query' => $questions, 'mots'=>'', 'nophoto' => true)); ?>
</div>
