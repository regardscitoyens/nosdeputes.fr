<?php
$sf_response->setTitle('question n°'.$question->numero.' de '.$parlementaire->nom.' au '.$question->uniqueMinistere());
?><div class="question" id="question<?php echo $question->numero ?>">
   <h1>Question n° <?php echo $question->numero; ?> de <?php echo link_to($parlementaire->nom, '@parlementaire?slug='.$parlementaire->slug) ?> au <?php echo $question->uniqueMinistere(); ?></h1>
  <p>Le <?php echo myTools::displayDate($question->date) ?></p>
  <p class="source"><a href="<?php echo $question->source; ?>">source</a></p>
    <div id="question"><h2>Question</h2><?php echo $question->question ?></div>
   <div id="reponse"><?php if (! empty($question->reponse)) {
   echo '<h2>Reponse</h2>';
   echo  $question->reponse;
 } else echo 'Cette question n\'a pas encore de réponse.' ?></div>
 <div class="commentaires">
 <h3>Commentaires</h3>
<?php echo include_component('commentaire', 'show', array('object' => $question)); ?>
<?php echo include_component('commentaire', 'form', array('object' => $question)); ?>
  </div>
</div>
