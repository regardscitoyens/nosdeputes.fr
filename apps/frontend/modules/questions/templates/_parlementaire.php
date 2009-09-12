<ul>
<?php $cpt = 0; foreach($questions as $question) : 
$cpt ++;
?>
<li><?php echo link_to($question['themes'],
		       '@question?id='.$question['id']); ?></li>
<?php if (isset($limit) && $cpt >= $limit) break; endforeach; ?>
</ul>
