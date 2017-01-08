<div class="question" id="question<?php echo $question->legislature."-".$question->numero ?>">
<?php
  $titre = $question->type.' N° '.$question->getShortNum();
  $sf_response->setTitle($parlementaire->nom.' - '.$titre.' : '.$question->titre." - NosSénateurs.fr");
  $titre .= ' au '.$question->uniqueMinistere();
  if ($question->motif_retrait === "caduque") $titre .= ' (caduque)';
  else if ($question->motif_retrait || ($question->date_cloture && !$question->reponse && (date("Y-m-d") > $question->date_cloture))) $titre .= ' (retirée)';
echo include_component('parlementaire', 'header', array('parlementaire' => $parlementaire, 'titre' => $titre, 'senateurfirst' => true));
?>
  <div class="source"><a href="<?php echo $question->source; ?>">source</a></div>
  <h1><?php echo $question->titre; ?></h1>
  <div id="question">
    <h2>Question soumise le <?php echo myTools::displayDate($question->date_cloture ? min($question->date, $question->date_cloture) : $question->date) ?></h2>
    <?php echo $question->getQuestionRiche(); ?>
  </div>
  <div id="reponse">
    <?php if ($question->motif_retrait || ($question->date_cloture && !$question->reponse && date("Y-m-d") > $question->date_cloture)) {
      echo '<h3>Retirée';
      if ($question->date_cloture) echo ' le '.myTools::displayDate($question->date_cloture);
      if ($question->motif_retrait && $question->motif_retrait != "retrait") echo ' ('.$question->motif_retrait.')';
      echo '</h3>';
    } else {
      echo '<h2>Réponse';
      if ($question->date_cloture) {
        if ($question->reponse) echo ' émise';
        else echo ' à venir';
        echo ' le '.myTools::displayDate($question->date_cloture);
      }
      echo '</h2>';
      if ($question->reponse)
        echo $question->getReponseRiche();
      else echo '<p>Cette question n\'a pas encore de réponse.</p>';
    } ?>
  </div>
</div>
<div class="commentaires">
<?php echo include_component('commentaire', 'showAll', array('object' => $question, 'type' => 'cette question'));
echo include_component('commentaire', 'form', array('object' => $question)); ?>
</div>
