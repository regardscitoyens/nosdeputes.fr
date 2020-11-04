<?php 
  foreach ($scrutins as $s) {
?>
<li>
	<a href="<?= $s->getScrutin()->getURL() ?>">
		<?= myTools::displayVeryShortDate($s->getScrutin()->date) ?> : <?= $s->getScrutin()->titre ?>
		(<strong class="vote-<?= $s->position ?>"><?= $s->position ?></strong>)
	</a>
</li>
<?php
  }
?>