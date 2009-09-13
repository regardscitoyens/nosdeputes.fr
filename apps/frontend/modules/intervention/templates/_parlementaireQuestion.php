<ul>
<?php $cpt = 0; foreach($questions as $question) :
$cpt ++;
$titre = myTools::displayDate($question->date).'&nbsp;: ';
$section = $question->getSection();
if (preg_match('/question/i', $section->getSection()->getTitre()))
  $titre .= ucfirst($section->getTitre());
else $titre .= ucfirst($section->getSection()->getTitre());
?>
  <li><?php echo link_to($titre, url_for('@interventions_seance?seance='.$question->getSeance()->id).'#table_'.$section->id); ?></li>
<?php if (isset($limit) && $cpt >= $limit) break; endforeach; ?>
</ul>