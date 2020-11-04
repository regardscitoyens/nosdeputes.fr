<ul>
<?php 
  foreach ($scrutins as $s) {
?>
<li>
	<a href="<?= $s->getScrutin()->getURL() ?>">
		<?= myTools::displayVeryShortDate($s->getScrutin()->date) ?> : <?= $s->getScrutin()->titre ?>
		(<strong class="vote-<?= $s->position ?>"><?= $s->getHumanPosition() ?></strong>)
	</a>
</li>
<?php
  }
?>
</ul>