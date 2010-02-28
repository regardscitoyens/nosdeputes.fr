<?php if (!count($questions)) { ?>
    <i>Désolé, le député n'a pas posé de question dans les derniers mois</i>
<?php return ;}?>
<ul>
<?php $cpt = 0; foreach($questions as $question) :
$cpt ++;
$titre = myTools::displayDate($question->date).'&nbsp;: '.$question->uniqueMinistere();
if ($theme = $question->firstTheme())
  $titre .= '&nbsp;('.$theme.')';
?>
  <li><?php echo link_to($titre, url_for('@question?id=QE'.$question->numero)); ?></li>
<?php if (isset($limit) && $cpt >= $limit) break; endforeach; ?>
</ul>
