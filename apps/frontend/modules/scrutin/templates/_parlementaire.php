<ul>
<?php 
  foreach ($votes as $v) {
?>
<li>
    <a href="<?= $v->getScrutin()->getURL() ?>">
        <?= myTools::displayVeryShortDate($v->getScrutin()->date) ?> :

        <strong class="vote-<?= $v->position ?>"><?= $v->getHumanPosition() ?></strong>

        <?php if ($v->mise_au_point_position) { ?>
            (mise au point: <strong class="vote-<?= $v->mise_au_point_position ?>"><?= $v->getHumanPositionMiseAuPoint() ?></strong>)
        <?php } ?>

        <?php if ($v->position == 'abstention' || $v->position == 'nonVotant') { ?>
            sur
        <?php } ?>

        <?= $v->getScrutin()->titre ?>
    </a>
</li>
<?php
  }
?>
</ul>