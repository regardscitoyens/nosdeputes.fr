<?php
$titre = 'Questions écrites';
$sf_response->setTitle($titre.' de '.$parlementaire->nom);
echo include_component('parlementaire', 'header', array('parlementaire' => $parlementaire, 'titre' => $titre, 'rss' => '@parlementaire_questions_rss?slug='.$parlementaire->slug));
?>
<?php echo include_component('questions', 'pagerQuestions', array('question_query' => $questions, 'mots'=>'', 'nophoto' => true)); ?>
