<?php 
  foreach ($scrutins as $s) {
?>
<li>
	<?= myTools::displayVeryShortDate($s->getScrutin()->date) ?> : <?= $s->getScrutin()->titre ?> (<?= $s->position ?>)
</li>
<?php
  }
?>