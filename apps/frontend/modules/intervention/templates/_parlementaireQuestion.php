<ul>
<?php $cpt = 0; foreach($questions as $question) :
$cpt ++;
$titre = myTools::displayDate($question->date).' &mdash; ';
$section = $question->getSection();
if ($section->getSection())
  $titre .= ucfirst($section->getSection()->getTitre()).'&nbsp;: ';
$titre .= ucfirst($section->getTitre());
?>
  <li><?php echo link_to($titre, url_for('@interventions_seance?seance='.$question->getSeance()->id).'#inter_'.$question->getMd5()); ?></li>
<?php if (isset($limit) && $cpt >= $limit) break; endforeach; ?>
</ul>