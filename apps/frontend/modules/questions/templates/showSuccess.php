<?php
$sf_response->setTitle('Question écrite N°'.$question->numero.' de '.$parlementaire->nom.' au '.$question->uniqueMinistere()); ?>
<div class="question" id="question<?php echo $question->numero ?>">
   <h1>Question N° <?php echo $question->numero; ?> de <?php echo link_to($parlementaire->nom, '@parlementaire?slug='.$parlementaire->slug) ?> au <?php echo $question->uniqueMinistere(); ?></h1>
  <div class="source"><a href="<?php echo $question->source; ?>">source</a></div>
  <p>Soumise le <?php echo myTools::displayDate($question->date) ?></p>
    <div id="question">
      <h2>Question</h2>
      <?php echo $question->question ?>
    </div>
    <div id="reponse">
      <h2>Réponse</h2>
      <?php if (! empty($question->reponse))
        echo  $question->reponse;
      else echo 'Cette question n\'a pas encore de réponse.' ?></div>
    <div class="commentaires">
      <h3>Commentaires</h3>
<?php echo include_component('commentaire', 'show', array('object' => $question)); ?>
<?php echo include_component('commentaire', 'form', array('object' => $question)); ?>
    </div>
</div>
