<ul>
<?php 
  foreach ($scrutins as $s) {
?>
<li>
	<a href="<?= $s->getScrutin()->getURL() ?>">
		<?= myTools::displayVeryShortDate($s->getScrutin()->date) ?> : <?= $s->getScrutin()->titre ?>
		(<strong class="vote-<?= $s->position ?>"><?= $s->position ?></strong>)
		<?php if ($s->mise_au_point_position) { ?>
			(mise au point: <strong class="vote-<?= $s->mise_au_point_position ?>"><?= $s->getHumanPositionMiseAuPoint() ?></strong>)
		<?php } ?>
	</a>
</li>
<?php
  }
?>
</ul>