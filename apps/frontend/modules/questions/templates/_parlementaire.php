<ul>
<?php $cpt = 0; foreach($questions as $question) :
$cpt ++;
$titre = myTools::displayDate($question->date).'&nbsp;: '.$question->uniqueMinistere().'&nbsp;(';
$titre .= $question->firstTheme().')';
?>
  <li><?php echo link_to($titre, url_for('@question?id='.$question->id)); ?></li>
<?php if (isset($limit) && $cpt >= $limit) break; endforeach; ?>
</ul>