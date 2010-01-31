<div class="question" id="question<?php echo $question->numero ?>">
<?php
  $titre = 'Question N° '.$question->numero.' au '.$question->uniqueMinistere();
$sf_response->setTitle($parlementaire->nom.' : '.$titre);
echo include_component('parlementaire', 'header', array('parlementaire' => $parlementaire, 'titre' => $titre, 'deputefirst' => true));
?>
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
    else echo 'Cette question n\'a pas encore de réponse.' ?>
  </div>
</div>
<div class="commentaires">
<?php if ($question->nb_commentaires == 0)
  echo '<h3>Aucun commentaire n\'a encore été formulé sur cette question</h3>';
else echo include_component('commentaire', 'showAll', array('object' => $question));
echo include_component('commentaire', 'form', array('object' => $question)); ?>
</div>
