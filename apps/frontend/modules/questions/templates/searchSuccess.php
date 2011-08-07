<?php
$sf_response->setTitle('Recherche de questions écrites parlant"'.$mots.'"');
echo include_component('questions', 'pagerQuestions', array('question_query' => $query, 'highlight' => $high, 'mots' => $mots));
?>
