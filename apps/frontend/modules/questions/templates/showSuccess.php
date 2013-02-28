<div class="question" id="question<?php echo $question->numero ?>">
<?php
  $titre = 'Question N° '.$question->numero.' au '.$question->uniqueMinistere();
  if ($question->date_cloture && !$question->reponse) $titre .= ' (retirée)';
  $sf_response->setTitle($parlementaire->nom.' : '.$titre);
  echo include_component('parlementaire', 'header', array('parlementaire' => $parlementaire, 'titre' => $titre, 'deputefirst' => true));
?>
  <div class="source"><a href="<?php echo $question->source; ?>">source</a></div>
  <div id="question">
<?php if (!$question->reponse) {
echo '<div class="alerte"><div class="mail"><h3 class="aligncenter">Être alerté lorsque cette<br>question aura une réponse</h3><table width="100%" style="text-align: center"><tbody><tr><td><a href="'.url_for('@alerte_question?num='.$question->numero).'"><img src="/images/xneth/email.png" alt="Email"></a><br><a href="'.url_for('@alerte_question?num='.$question->numero).'">par email</a></td><td></td></tr></tbody></table></div></div>';
} ?>
    <h2>Question soumise le <?php echo myTools::displayDate($question->date) ?></h2>
<?php if (!$question->reponse) {
echo '<div class="alerte"><div class="mail"><h3 class="aligncenter">Être alerté lorsque cette<br>question aura une réponse</h3><table width="100%" style="text-align: center"><tbody><tr><td><a href="'.url_for('@alerte_question?num='.$question->numero).'"><img src="/images/xneth/email.png" alt="Email"></a><br><a href="'.url_for('@alerte_question?num='.$question->numero).'">par email</a></td><td></td></tr></tbody></table></div></div>';
} ?>
    <?php echo '<p>'.myTools::displayDate($question->question).'</p>' ?>
  </div>
  <div id="reponse">
    <?php if ($question->date_cloture && !$question->reponse && date("Y-m-d") > $question->date_cloture) {
      echo '<h3>Retirée le '.myTools::displayDate($question->date_cloture);
      if ($question->motif_retrait) echo ' ('.$question->motif_retrait.')';
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
        echo '<p>'.myTools::escape_blanks($question->reponse).'</p>';
      else {
        echo '<p>Cette question n\'a pas encore de réponse.</p>';
//        echo '<div class="source alerte"><a href="'.url_for('@alerte_question?num='.$question->numero).'">'.image_tag('xneth/email.png')."<br/>être informé<br/>par e-mail de<br/>la réponse".'</a></div>';
      }
    } ?>
  </div>
</div>
<div class="commentaires">
<?php echo include_component('commentaire', 'showAll', array('object' => $question, 'type' => 'cette question'));
echo include_component('commentaire', 'form', array('object' => $question)); ?>
</div>
