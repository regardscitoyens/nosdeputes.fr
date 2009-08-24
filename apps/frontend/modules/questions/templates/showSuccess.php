<div class="temp">
<div class="question" id="question<?php echo $question->numero ?>">
  <h1>Question N° <?php echo $question->numero; ?></h1>
  <p class="infos">Par <?php echo link_to($parlementaire->nom, '@parlementaire?slug='.$parlementaire->slug) ?>, le <?php echo $question->date ?></p>
  <p class="source"><a href="http://questions.assemblee-nationale.fr/<?php echo $question->source; ?>">source</a>
    <div id="question"><?php echo $question->question ?></div>
    <div id="reponse"><?php echo (! empty($question->reponse)) ? $question->reponse : 'Cette question n\'a pas encore de réponse.' ?></div>
 <div class="commentaires">
    3 commentaires dont celui de zouze :
    Cette question tue des ornithorynques !
  </div>
</div>
</div>
