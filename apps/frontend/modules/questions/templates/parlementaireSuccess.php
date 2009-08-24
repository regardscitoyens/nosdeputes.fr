<div class="temp">
<div class="titre_questions_perso">
<h1>Les questions de <a href="<?php echo url_for($parlementaire->getPageLink()); ?>"><?php echo $parlementaire->nom.' '; if ($parlementaire->getPhoto()) echo image_tag($parlementaire->getPhoto(), ' alt=Photo de '.$parlementaire->nom); ?></a></h1>

</div>
<div class="questions">
<?php if(count($questions) < 1) : ?>
Ce député n'a posé aucune question.
<?php else : ?>
<table><tr><th>Numéro</th><th>Ministères</th><th>Thèmes</th></tr>
<?php foreach($questions as $question) : ?>
<tr>
<td><?php echo link_to($question->numero, '@parlementaire_question?id=' . $question->id) ?></td>
<td><?php echo $question->ministere ?></td>
<td><?php echo $question->themes ?></td>
</tr>
<?php endforeach ?>
</table>
<?php endif ?>
</div>
</div>
