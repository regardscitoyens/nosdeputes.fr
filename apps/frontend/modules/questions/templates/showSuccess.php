<div class="temp">
<div class="question" id="question<?php echo $question->numero ?>">
  <h1>Question N° <?php echo $question->numero; ?></h1>
  <p class="infos">Par <?php echo link_to($parlementaire->nom, '@parlementaire?slug='.$parlementaire->slug) ?>, le <?php echo $question->date ?></p>
  <p class="source"><a href="<?php echo $question->source; ?>">source</a>
    <div id="question"><?php echo $question->question ?></div>
    <div id="reponse"><?php echo (! empty($question->reponse)) ? $question->reponse : 'Cette question n\'a pas encore de réponse.' ?></div>
 <div class="commentaires">
 <h3>Commentaires</h3>
<?php echo include_component('commentaire', 'show', array('object' => $question)); ?>
<?php echo include_component('commentaire', 'form', array('object' => $question)); ?>
  </div>
</div>
</div>
